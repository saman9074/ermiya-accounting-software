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

        // Additional validation for stock
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['items' => "موجودی کالای '{$product->name}' کافی نیست (موجودی فعلی: {$product->stock})"]);
            }
        }


        $invoice = DB::transaction(function () use ($validated) {
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $invoice = Invoice::create([
                'person_id' => $validated['person_id'],
                'invoice_number' => 'INV-' . time(), // Simple unique number for now
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Decrement stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
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
