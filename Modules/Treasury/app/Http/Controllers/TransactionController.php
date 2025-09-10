<?php

namespace Modules\Treasury\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\Transaction;
use Modules\Treasury\Models\Account;
use Inertia\Inertia;
use Modules\Core\Rules\DateWithinFinancialYear;

class TransactionController extends Controller
{

    public function index()
    {
        return Inertia::render('Treasury::Transactions/Index', [
            'transactions' => Transaction::with(['account', 'transactionable'])->orderBy('transaction_date', 'desc')->get(),
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
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => ['required', 'date', new DateWithinFinancialYear], // <-- از قانون جدید استفاده می‌کنیم
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated, $transaction) {
            $oldAmount = $transaction->amount;
            $oldAccount = $transaction->account;

            $newAmount = $validated['amount'];
            $newAccount = Account::find($validated['account_id']);

            // Reverse the old transaction's effect
            $oldAccount->decrement('current_balance', $oldAmount);
            if ($transaction->transactionable_type === Invoice::class) {
                $transaction->transactionable->decrement('paid_amount', $oldAmount);
            }

            // Apply the new transaction's effect
            $newAccount->increment('current_balance', $newAmount);
            if ($transaction->transactionable_type === Invoice::class) {
                $transaction->transactionable->increment('paid_amount', $newAmount);
                $transaction->transactionable->payment_status = $transaction->transactionable->paid_amount >= $transaction->transactionable->total_amount ? 'paid' : 'partial';
                if ($transaction->transactionable->paid_amount <= 0) {
                    $transaction->transactionable->payment_status = 'unpaid';
                }
                $transaction->transactionable->save();
            }

            // Update the transaction itself
            $transaction->update($validated);
        });

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }


    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            // Reverse the financial effects before deleting
            $account = $transaction->account;
            $account->decrement('current_balance', $transaction->amount);

            if ($transaction->transactionable_type === Invoice::class) {
                $invoice = $transaction->transactionable;
                $invoice->decrement('paid_amount', $transaction->amount);

                // Update invoice status after reversal
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $invoice->payment_status = 'paid';
                } elseif ($invoice->paid_amount > 0 && $invoice->paid_amount < $invoice->total_amount) {
                    $invoice->payment_status = 'partial';
                } else {
                    $invoice->payment_status = 'unpaid';
                }
                $invoice->save();
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت حذف شد.');
    }
}
