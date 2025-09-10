<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Inventory\Models\Product;
use Modules\Persons\Models\Person;
use Modules\Sales\Models\Invoice;
use Modules\Sales\Models\InvoiceItem;
use Modules\Treasury\Models\Account;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    return Inertia::render('Sales::Invoices/Index', [
        'invoices' => Invoice::with('person')->latest()->get(),
        // Pass the success message from the session to the view
        'success' => session('success'),
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return Inertia::render('Sales::Invoices/Create', [
        'persons' => Person::all(['id', 'name']),
        'products' => Product::all(['id', 'name', 'sale_price']),
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:persons,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            foreach ($validated['items'] as $itemData) {
                $totalAmount += $itemData['quantity'] * $itemData['unit_price'];
            }

            $invoice = Invoice::create([
                'person_id' => $validated['person_id'],
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['items'] as $itemData) {
                // *** FIX: Calculate and add total_price for each item ***
                $invoice->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                ]);
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'فاکتور جدید با موفقیت صادر شد.');
    }

    public function show(Invoice $invoice)
    {
        return Inertia::render('Sales::Invoices/Show', [
            'invoice' => $invoice->load('person', 'items.product'),
            'accounts' => Account::all(), // <-- Pass accounts to the view
            'success' => session('success'),
        ]);
    }
}
