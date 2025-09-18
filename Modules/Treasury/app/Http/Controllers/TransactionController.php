<?php

namespace Modules\Treasury\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\ExpenseCategory;
use Modules\Treasury\Models\Payee;
use Modules\Treasury\Models\Transaction;
use Modules\Treasury\Models\Account;
use Inertia\Inertia;
use Modules\Core\Rules\DateWithinFinancialYear;
use Modules\Core\Models\Currency;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        $transactions = Transaction::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('amount', '=', $search);
            })
            ->with(['account', 'transactionable', 'expenseCategory', 'payee'])
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Treasury::Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required_if:amount,>,0|nullable|exists:accounts,id', // فقط اگر وجه نقدی دریافت می‌شود، حساب اجباری است
            'amount' => 'required|numeric|min:0', // می‌تواند صفر باشد اگر فقط از اعتبار استفاده شود
            'apply_credit' => 'required|numeric|min:0', // مبلغ استفاده از اعتبار
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'invoice_id' => 'required|exists:invoices,id'
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // اطمینان از اینکه مجموع پرداخت و اعتبار از مانده فاکتور بیشتر نشود
        $remainingBalance = $invoice->total_amount - $invoice->paid_amount;
        if(($validated['amount'] + $validated['apply_credit']) > $remainingBalance) {
            return back()->withErrors(['amount' => 'مجموع مبلغ پرداختی و اعتبار استفاده شده از مانده فاکتور بیشتر است.']);
        }

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        DB::transaction(function () use ($validated, $invoice, $divisor) {
            // بخش اول: ثبت دریافت وجه نقد/بانکی (اگر وجود داشته باشد)
            if ($validated['amount'] > 0) {
                $amountInRials = $validated['amount'] * $divisor;
                Transaction::create([
                    'account_id' => $validated['account_id'],
                    'type' => 'income',
                    'amount' => $amountInRials,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'],
                    'transactionable_id' => $invoice->id,
                    'transactionable_type' => Invoice::class,
                ]);
                // به‌روزرسانی موجودی حساب
                Account::find($validated['account_id'])->increment('current_balance', $amountInRials);
            }

            // بخش دوم: ثبت تراکنش استفاده از اعتبار (اگر وجود داشته باشد)
            if ($validated['apply_credit'] > 0) {
                $creditInRials = $validated['apply_credit'] * $divisor;
                Transaction::create([
                    'account_id' => null, // استفاده از اعتبار به حساب بانکی واریز نمی‌شود
                    'type' => 'income',
                    'amount' => $creditInRials, // این هم یک نوع "دریافتی" برای فاکتور محسوب می‌شود
                    'transaction_date' => $validated['transaction_date'],
                    'description' => 'استفاده از اعتبار بستانکاری برای فاکتور شماره ' . $invoice->id,
                    'transactionable_id' => $invoice->id,
                    'transactionable_type' => Invoice::class,
                ]);
            }

            // در نهایت، وضعیت فاکتور را بر اساس تمام تراکنش‌های جدید و قدیم به‌روز کن
            $invoice->updateStatus();
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'عملیات پرداخت با موفقیت ثبت شد.');
    }


    public function edit(Transaction $transaction)
    {
        return Inertia::render('Treasury::Transactions/Edit', [
            'transaction' => $transaction,
            'accounts' => Account::all(),
            'categories' => ExpenseCategory::all(),
            'payees' => Payee::all(),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'payee_id' => 'nullable|exists:payees,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;


        // تبدیل مبلغ جدید به ریال
        if ($divisor > 1) {
            $validated['amount'] = ($validated['amount'] ?? 0) * $divisor;
        }

        // Note: Reversing old transaction and creating a new one is safer for complex accounting.
        // For simplicity, we are updating in-place.
        DB::transaction(function () use ($validated, $request, $transaction) {
            $oldAmount = $transaction->amount;
            $oldAccountId = $transaction->account_id;

            $path = $transaction->attachment;
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
                $path = $request->file('attachment')->store('attachments', 'public');
            }

            // Update transaction details
            $transaction->update([
                'account_id' => $validated['account_id'],
                'expense_category_id' => $validated['expense_category_id'] ?? null,
                'payee_id' => $validated['payee_id'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'amount' => $transaction->type === 'expense' ? -abs($validated['amount']) : abs($validated['amount']),
                'description' => $validated['description'],
                'attachment' => $path,
            ]);

            // Update account balances
            // 1. Revert old transaction amount
            if ($oldAccountId) {
                Account::find($oldAccountId)->increment('current_balance', abs($oldAmount));
            }

            // 2. Apply new transaction amount
            Account::find($validated['account_id'])->decrement('current_balance', abs($validated['amount']));

        });

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }


    public function destroy(Transaction $transaction)
    {
        DB::transaction(function() use ($transaction){
            if($transaction->account_id){
                $amountToRevert = $transaction->amount;
                // If it's an expense (negative), adding it will increase the balance.
                // If it's an income (positive), subtracting it will decrease the balance.
                $transaction->account->increment('current_balance', -$amountToRevert);
            }
            // Delete attachment if exists
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }

            if ($transaction->transactionable_type === Invoice::class) {
                $invoice = $transaction->transactionable;
                $invoice->decrement('paid_amount', $transaction->amount);
                // Update invoice status here as well, similar to the SalesReturn fix.
                if ($invoice->paid_amount <= 0) {
                    $invoice->status = 'unpaid';
                } else {
                    $invoice->status = 'partially_paid';
                }
                $invoice->save();
            }
            $transaction->delete();
        });

        return back()->with('success', 'تراکنش با موفقیت حذف شد.');
    }
}
