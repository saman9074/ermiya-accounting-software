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
        // Load the items of the original invoice to show in the form
        $invoice->load('items.product');

        return Inertia::render('Sales::SalesReturns/Create', [
            'invoice' => $invoice
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'return_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // TODO: Validate that returned quantity is not more than sold quantity

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
}
