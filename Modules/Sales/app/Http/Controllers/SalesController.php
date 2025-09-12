<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Core\Rules\DateWithinFinancialYear;
use Modules\Inventory\Models\Product;
use Modules\Persons\Models\Person;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\Account;

class SalesController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('person')->latest()->get();
        return Inertia::render('Sales::Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        return Inertia::render('Sales::Invoices/Create', [
            'persons' => Person::all(),
            'products' => Product::where('stock', '>', 0)->get(), // Only show products in stock
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:persons,id',
            'issue_date' => ['required', 'date', new DateWithinFinancialYear],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['items' => "موجودی کالای '{$product->name}' کافی نیست (موجودی فعلی: {$product->stock})"]);
            }
        }

        $invoice = DB::transaction(function () use ($validated) {
            $totalAmount = collect($validated['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $invoice = Invoice::create([
                'person_id' => $validated['person_id'],
                'invoice_number' => 'INV-' . time(),
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = $invoice->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);

                $product = Product::find($itemData['product_id']);
                $product->decrement('stock', $itemData['quantity']);

                // Create a stock movement record
                StockMovement::create([
                    'product_id' => $product->id,
                    'reference_id' => $item->id,
                    'reference_type' => get_class($item),
                    'type' => 'sale',
                    'quantity_change' => -$itemData['quantity'],
                    'stock_after' => $product->fresh()->stock,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'فاکتور جدید با موفقیت صادر شد.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['person', 'items.product']);

        return Inertia::render('Sales::Invoices/Show', [
            'invoice' => $invoice,
            'accounts' => Account::all(),
        ]);
    }
}
