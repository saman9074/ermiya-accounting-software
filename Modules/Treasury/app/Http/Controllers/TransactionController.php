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
        $invoice = Invoice::findOrFail($request->input('invoice_id'));

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $invoice->remaining_amount],
            'transaction_date' => ['required', 'date', new DateWithinFinancialYear], // <-- از قانون جدید استفاده می‌کنیم
            'invoice_id' => 'required|exists:invoices,id',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            // 1. Create the transaction record
            $transaction = $invoice->transactions()->create([
                'account_id' => $request->input('account_id'),
                'amount' => $request->input('amount'),
                'type' => 'income',
                'transaction_date' => $request->input('transaction_date'),
                'description' => $request->input('description'),
            ]);

            // 2. Update the account's current balance
            $account = Account::find($request->input('account_id'));
            $account->increment('current_balance', $transaction->amount);

            // 3. Update the invoice's paid amount and status
            $invoice->increment('paid_amount', $transaction->amount);
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->payment_status = 'paid';
            } else {
                $invoice->payment_status = 'partial';
            }
            $invoice->save();
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'دریافت وجه با موفقیت ثبت شد.');
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

            $transaction->delete();
        });

        return back()->with('success', 'تراکنش با موفقیت حذف شد.');
    }
}
