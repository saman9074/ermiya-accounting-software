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

class SalesReturnController extends Controller
{
    public function create(Invoice $invoice)
    {
        $invoice->load('items.product');

        // محاسبه تعداد کالاهایی که قبلا برگشت داده شده‌اند
        $previouslyReturnedItems = SalesReturnItem::whereHas('salesReturn', function ($query) use ($invoice) {
            $query->where('invoice_id', $invoice->id);
        })
            ->select('product_id', DB::raw('SUM(quantity) as total_returned'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // اضافه کردن تعداد قابل برگشت به هر آیتم فاکتور
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

        // اعتبارسنجی سفارشی برای هر آیتم
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'return_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request, $invoice) {
                    // $attribute will be something like 'items.0.quantity'
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

        $validated = $request->all(); // all data is now validated

        $totalReturnAmount = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $salesReturn = DB::transaction(function () use ($validated, $invoice, $totalReturnAmount) {

            // ۱. ایجاد سند اصلی برگشت از فروش
            $salesReturn = SalesReturn::create([
                'invoice_id' => $invoice->id,
                'person_id' => $invoice->person_id,
                'return_date' => $validated['return_date'],
                'total_amount' => $totalReturnAmount,
                'description' => $validated['description'],
            ]);

            foreach ($validated['items'] as $itemData) {
                // ۲. محاسبه مبلغ کل آیتم و ایجاد آیتم‌های برگشتی
                $totalItemAmount = $itemData['quantity'] * $itemData['unit_price'];

                $returnItem = $salesReturn->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_amount' => $totalItemAmount, // فیلد اصلاح شده
                ]);

                $product = Product::find($itemData['product_id']);

                // ۳. افزایش موجودی انبار
                $newStock = $product->stock + $itemData['quantity'];
                $product->update(['stock' => $newStock]);

                // ۴. ثبت در کاردکس انبار
                StockMovement::create([
                    'product_id' => $product->id,
                    'reference_id' => $returnItem->id,
                    'reference_type' => SalesReturnItem::class, // استفاده از کلاس مدل
                    'type' => 'sale_return',
                    'quantity_change' => $itemData['quantity'], // مقدار مثبت چون به انبار اضافه شده
                    'stock_after' => $newStock,
                ]);
            }

            // ۵. ایجاد تراکنش مالی برای بستانکار کردن حساب مشتری
            // ما یک تراکنش از نوع درآمد با مبلغ منفی ثبت می‌کنیم که معادل اعتبار است
            $salesReturn->transactions()->create([
                'type' => 'income',
                'amount' => -$totalReturnAmount, // مبلغ منفی برای ایجاد اعتبار
                'transaction_date' => $validated['return_date'],
                'description' => 'بابت فاکتور برگشت از فروش شماره ' . $salesReturn->id,
                'account_id' => null, // چون این یک اعتبار است و به حساب بانکی/صندوق واریز نشده
            ]);

            return $salesReturn;
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'فاکتور برگشت از فروش با موفقیت ثبت شد.');
    }

    public function index()
    {
        $salesReturns = SalesReturn::with('person', 'invoice')
            ->latest()
            ->paginate(10);

        return Inertia::render('Sales::SalesReturns/Index', [
            'salesReturns' => $salesReturns,
        ]);
    }

    public function destroy(SalesReturn $salesReturn)
    {
        DB::transaction(function () use ($salesReturn) {
            // ۱. برگرداندن موجودی انبار و حذف کاردکس
            foreach ($salesReturn->items as $item) {
                $product = $item->product;

                // کاهش موجودی انبار
                $newStock = $product->stock - $item->quantity;
                $product->update(['stock' => $newStock]);

                // حذف رکورد کاردکس مرتبط (اگر با cascade on delete حذف نشود)
                StockMovement::where('reference_type', SalesReturnItem::class)
                    ->where('reference_id', $item->id)
                    ->delete();
            }

            // ۲. حذف تراکنش مالی مرتبط
            $salesReturn->transactions()->delete();

            // ۳. حذف خود سند (آیتم‌ها به دلیل cascade on delete خودکار حذف می‌شوند)
            $salesReturn->delete();
        });

        return redirect()->route('sales_returns.index')->with('success', 'سند برگشت از فروش با موفقیت حذف شد.');
    }
}
