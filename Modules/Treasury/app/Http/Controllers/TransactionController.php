<?php

namespace Modules\Treasury\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Invoice;
use Modules\Treasury\Models\Transaction;
use Modules\Treasury\Models\Account;
use Inertia\Inertia;
class TransactionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['account', 'transactionable'])
            ->latest()
            ->get();

        return Inertia::render('Treasury::Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        return Inertia::render('Treasury::Transactions/Edit', [
            'transaction' => $transaction,
            'accounts' => Account::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // For MVP, we keep it simple. A full implementation would handle
        // reverting old amounts and applying new ones to accounts and invoices.
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transaction->update($validated);

        // A full implementation requires recalculating invoice and account balances.

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            // 1. Revert the financial impact on the account
            $account = $transaction->account;
            if ($transaction->type === 'income') {
                $account->current_balance -= $transaction->amount;
            } else {
                $account->current_balance += $transaction->amount;
            }
            $account->save();

            // 2. Revert the impact on the related model (e.g., Invoice)
            if ($transaction->transactionable_type === Invoice::class) {
                $invoice = $transaction->transactionable;
                $invoice->paid_amount -= $transaction->amount;

                if ($invoice->paid_amount <= 0) {
                    $invoice->payment_status = 'unpaid';
                } else {
                    $invoice->payment_status = 'partial';
                }
                $invoice->save();
            }

            // 3. Delete the transaction itself
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'تراکنش با موفقیت حذف شد.');
    }

    /**
     * Store a newly created transaction for a sales invoice.
     */
    public function storeForInvoice(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            // 1. Create the transaction
            $invoice->transactions()->create([
                'account_id' => $validated['account_id'],
                'amount' => $validated['amount'],
                'type' => 'income',
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
            ]);

            // 2. Update invoice status
            $invoice->paid_amount += $validated['amount'];
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->payment_status = 'paid';
            } else {
                $invoice->payment_status = 'partial';
            }
            $invoice->save();

            // 3. UPDATE THE ACCOUNT BALANCE (The Fix!)
            $account = Account::find($validated['account_id']);
            $account->current_balance += $validated['amount'];
            $account->save();
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'دریافت وجه با موفقیت ثبت شد.');
    }
}
