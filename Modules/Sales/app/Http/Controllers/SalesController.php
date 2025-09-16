<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Core\Models\Setting;
use Modules\Core\Rules\DateWithinFinancialYear;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Modules\Persons\Models\Person;
use Modules\Sales\Models\Invoice;
use Modules\Sales\Models\InvoiceItem;
use Modules\Treasury\Models\Account;
use Modules\Core\Models\Currency;
class SalesController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('id', $search) // جستجو بر اساس شماره فاکتور
                ->orWhereHas('person', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%"); // جستجو بر اساس نام مشتری
                });
            })
            ->with('person')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales::Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search']),
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
            'notes' => 'nullable|string',
        ]);

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        $invoiceData = DB::transaction(function () use ($validated, $divisor) {
            $subtotalAmount = 0;
            $invoiceItemsData = [];

            // ابتدا تمام محاسبات را انجام می‌دهیم و سپس مقادیر نهایی را به ریال تبدیل می‌کنیم
            foreach ($validated['items'] as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                $discountAmount = 0;

                if (!empty($itemData['discount_type']) && !empty($itemData['discount_value'])) {
                    if ($itemData['discount_type'] === 'percentage') {
                        $discountAmount = ($itemSubtotal * $itemData['discount_value']) / 100;
                    } else {
                        // تخفیف مبلغی برای هر واحد است
                        $discountAmount = $itemData['discount_value'] * $itemData['quantity'];
                    }
                }

                $totalItemAmount = $itemSubtotal - $discountAmount;
                $subtotalAmount += $totalItemAmount;

                $invoiceItemsData[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    // تبدیل مبالغ هر آیتم به ریال
                    'unit_price' => $itemData['unit_price'] * $divisor,
                    'discount_type' => $itemData['discount_type'] ?? null,
                    'discount_value' => $itemData['discount_value'] ?? 0,
                    'discount_amount' => $discountAmount * $divisor,
                    'total_price' => $totalItemAmount * $divisor,
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
                // تبدیل مبالغ نهایی فاکتور به ریال
                'subtotal_amount' => $subtotalAmount * $divisor,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'discount_amount' => $overallDiscountAmount * $divisor,
                'total_amount' => $finalTotalAmount * $divisor,
                'paid_amount' => 0,
                'status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
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

    public function print(Invoice $invoice)
    {
        $invoice->load(['person', 'items.product']);
        $companySettings = Setting::all()->pluck('value', 'key');

        return Inertia::render('Sales::Invoices/Print', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ]);
    }

    public function receivePayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => ['required', 'date', new DateWithinFinancialYear],
            'description' => 'nullable|string',
        ]);

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        // تبدیل مبلغ دریافتی به ریال
        $paymentAmountInRials = $validated['amount'] * $divisor;

        // اطمینان از اینکه مبلغ دریافتی از مانده فاکتور بیشتر نباشد
        $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
        if ($paymentAmountInRials > $remainingAmount) {
            return back()->withErrors(['amount' => 'مبلغ دریافتی نمی‌تواند از مانده فاکتور بیشتر باشد.']);
        }

        DB::transaction(function () use ($invoice, $validated, $paymentAmountInRials) {
            // ۱. ثبت تراکنش مالی
            $invoice->transactions()->create([
                'account_id' => $validated['account_id'],
                'type' => 'income',
                'amount' => $paymentAmountInRials,
                'transaction_date' => $validated['payment_date'],
                'description' => $validated['description'] ?? 'دریافت وجه بابت فاکتور فروش شماره ' . $invoice->id,
            ]);

            // ۲. افزایش موجودی حساب بانکی/صندوق
            $account = Account::find($validated['account_id']);
            $account->increment('current_balance', $paymentAmountInRials);

            // ۳. به‌روزرسانی مبلغ پرداخت شده فاکتور
            $invoice->increment('paid_amount', $paymentAmountInRials);

            // ۴. به‌روزرسانی وضعیت فاکتور
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partially_paid']);
            }
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'دریافت وجه با موفقیت ثبت شد.');
    }
}
