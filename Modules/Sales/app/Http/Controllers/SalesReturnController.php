<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Modules\Sales\Models\Invoice;
use Modules\Sales\Models\SalesReturn;
use Modules\Treasury\Models\Transaction;
use Modules\Sales\Models\SalesReturnItem;
use Modules\Core\Models\Currency;
use Modules\Core\Rules\DateWithinFinancialYear;

class SalesReturnController extends Controller
{
    public function create(Invoice $invoice)
    {
        $invoice->load('items.product.unit');

        $previouslyReturnedItems = SalesReturnItem::whereHas('salesReturn', function ($query) use ($invoice) {
            $query->where('invoice_id', $invoice->id);
        })
            ->select('product_id', DB::raw('SUM(quantity) as total_returned'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $invoice->items->each(function ($item) use ($previouslyReturnedItems) {
            $returnedQty = $previouslyReturnedItems->get($item->product_id)->total_returned ?? 0;
            $item->returnable_quantity = $item->quantity - $returnedQty;
        });

        return Inertia::render('Sales::SalesReturns/Create', [
            'invoice' => $invoice
        ]);
    }

    public function store(Request $request)
    {
        $invoice = Invoice::findOrFail($request->input('invoice_id'));

        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'return_date' => ['required', 'date', new DateWithinFinancialYear],
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => [
                'required', 'numeric', 'min:0.01',
                function ($attribute, $value, $fail) use ($request, $invoice) {
                    $index = explode('.', $attribute)[1];
                    $productId = $request->input("items.$index.product_id");

                    $invoiceItem = $invoice->items()->where('product_id', $productId)->first();
                    if (!$invoiceItem) {
                        return $fail('این کالا در فاکتور اصلی وجود ندارد.');
                    }

                    $soldQty = $invoiceItem->quantity;

                    $returnedQty = SalesReturnItem::whereHas('salesReturn', function ($q) use ($invoice) {
                        $q->where('invoice_id', $invoice->id);
                    })->where('product_id', $productId)->sum('quantity');

                    $returnableQty = $soldQty - $returnedQty;

                    if ($value > $returnableQty) {
                        $fail("شما حداکثر می‌توانید {$returnableQty} عدد از این کالا را مرجوع کنید.");
                    }
                },
            ],
        ]);

        $validated = $request->all();
        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        $totalReturnAmount = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $totalReturnAmountInRials = $totalReturnAmount * $divisor;

        $salesReturn = DB::transaction(function () use ($validated, $invoice, $totalReturnAmountInRials, $divisor) {

            $salesReturn = SalesReturn::create([
                'invoice_id' => $invoice->id,
                'person_id' => $invoice->person_id,
                'return_date' => $validated['return_date'],
                'total_amount' => $totalReturnAmountInRials,
                'description' => $validated['description'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $totalItemAmount = $itemData['quantity'] * $itemData['unit_price'];

                $returnItem = $salesReturn->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'] * $divisor,
                    'total_amount' => $totalItemAmount * $divisor,
                ]);

                $product = Product::find($itemData['product_id']);
                $newStock = $product->stock + $itemData['quantity'];
                $product->update(['stock' => $newStock]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'reference_id' => $returnItem->id,
                    'reference_type' => SalesReturnItem::class,
                    'type' => 'sale_return',
                    'quantity_change' => $itemData['quantity'],
                    'stock_after' => $newStock,
                ]);
            }

            $salesReturn->transactions()->create([
                'type' => 'income',
                'amount' => -$totalReturnAmountInRials,
                'transaction_date' => $validated['return_date'],
                'description' => 'بابت فاکتور برگشت از فروش شماره ' . $salesReturn->id,
                'account_id' => null,
            ]);

            // به‌روزرسانی مجدد مبلغ پرداختی و وضعیت فاکتور اصلی
            $totalPaid = $invoice->transactions()->where('type', 'income')->sum('amount') - $totalReturnAmountInRials;
            $invoice->paid_amount = $totalPaid;

            if (abs($invoice->paid_amount - $invoice->total_amount) < 0.01) {
                $invoice->status = 'paid';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partially_paid';
            } else {
                $invoice->status = 'unpaid';
            }
            $invoice->save();


            return $salesReturn;
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'فاکتور برگشت از فروش با موفقیت ثبت شد.');
    }

    public function index(Request $request)
    {
        $salesReturns = SalesReturn::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('id', $search)
                    ->orWhereHas('person', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->with('person', 'invoice')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales::SalesReturns/Index', [
            'salesReturns' => $salesReturns,
            'filters' => $request->only(['search']),
        ]);
    }

    public function destroy(SalesReturn $salesReturn)
    {
        DB::transaction(function () use ($salesReturn) {
            $invoice = $salesReturn->invoice; // <-- فاکتور مرجع را می‌گیریم

            // ۱. برگرداندن موجودی انبار
            foreach ($salesReturn->items as $item) {
                $product = $item->product;
                $newStock = $product->stock - $item->quantity;
                $product->update(['stock' => $newStock]);
                StockMovement::where('reference_type', SalesReturnItem::class)
                    ->where('reference_id', $item->id)
                    ->delete();
            }

            // ۲. حذف تراکنش مالی و خود سند
            $salesReturn->transactions()->delete();
            $salesReturn->delete();

            // ۳. اصلاح ۲: به‌روزرسانی مجدد مبالغ و وضعیت فاکتور اصلی
            if ($invoice) {
                // تمام تراکنش‌های باقیمانده (پرداختی‌ها و برگشتی‌های دیگر) را مجددا جمع می‌زنیم
                $totalPaidAndReturns = $invoice->transactions()->where('type', 'income')->sum('amount');
                $invoice->paid_amount = $totalPaidAndReturns;

                // ارزیابی مجدد وضعیت فاکتور
                if (abs($invoice->paid_amount - $invoice->total_amount) < 0.01) {
                    $invoice->status = 'paid';
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->status = 'partially_paid';
                } else {
                    $invoice->status = 'unpaid';
                }
                $invoice->save();
            }
        });

        // اصلاح ۳: ریدایرکت به صفحه نمایش فاکتور
        return redirect()->route('invoices.show', $salesReturn->invoice_id)->with('success', 'سند برگشت از فروش با موفقیت حذف شد.');
    }
}
