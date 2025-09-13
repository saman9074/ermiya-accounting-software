<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Core\Rules\DateWithinFinancialYear;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Modules\Persons\Models\Person;
use Modules\Sales\Models\Invoice;
use Modules\Sales\Models\InvoiceItem;
use Modules\Treasury\Models\Account;

class SalesController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('person')->latest()->paginate(10);
        return Inertia::render('Sales::Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        $persons = Person::with('group.priceList')->get();
        $products = Product::with(['unit', 'productPrices.priceList'])->get();

        return Inertia::render('Sales::Invoices/Create', [
            'persons' => $persons,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:persons,id',
            'issue_date' => ['required', 'date', new DateWithinFinancialYear],
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:percentage,amount',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,amount',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        $invoiceData = DB::transaction(function () use ($validated) {
            $subtotalAmount = 0;
            $invoiceItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                $discountAmount = 0;

                if (!empty($itemData['discount_type']) && !empty($itemData['discount_value'])) {
                    if ($itemData['discount_type'] === 'percentage') {
                        $discountAmount = ($itemSubtotal * $itemData['discount_value']) / 100;
                    } else {
                        $discountAmount = $itemData['discount_value'] * $itemData['quantity'];
                    }
                }

                $totalItemAmount = $itemSubtotal - $discountAmount;
                $subtotalAmount += $totalItemAmount;

                $invoiceItemsData[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_type' => $itemData['discount_type'] ?? null,
                    'discount_value' => $itemData['discount_value'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'total_price' => $totalItemAmount, // **اصلاحیه اصلی**
                ];
            }

            $overallDiscountAmount = 0;
            if (!empty($validated['discount_type']) && !empty($validated['discount_value'])) {
                if ($validated['discount_type'] === 'percentage') {
                    $overallDiscountAmount = ($subtotalAmount * $validated['discount_value']) / 100;
                } else {
                    $overallDiscountAmount = $validated['discount_value'];
                }
            }

            $finalTotalAmount = $subtotalAmount - $overallDiscountAmount;

            $invoice = Invoice::create([
                'person_id' => $validated['person_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal_amount' => $subtotalAmount,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'discount_amount' => $overallDiscountAmount,
                'total_amount' => $finalTotalAmount,
                'paid_amount' => 0,
                'status' => 'unpaid',
            ]);

            foreach ($invoiceItemsData as $itemData) {
                $invoiceItem = $invoice->items()->create($itemData);

                $product = Product::find($itemData['product_id']);
                $newStock = $product->stock - $itemData['quantity'];
                $product->update(['stock' => $newStock]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'reference_id' => $invoiceItem->id,
                    'reference_type' => InvoiceItem::class,
                    'type' => 'sale',
                    'quantity_change' => -$itemData['quantity'],
                    'stock_after' => $newStock,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoiceData->id)->with('success', 'فاکتور با موفقیت ایجاد شد.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['person', 'items.product']);
        $companySettings = \Modules\Core\Models\Setting::all()->pluck('value', 'key');

        return Inertia::render('Sales::Invoices/Show', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
            'accounts' => Account::all(),
        ]);
    }
}
