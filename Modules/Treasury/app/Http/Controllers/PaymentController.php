<?php
namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Treasury\Models\Account;
use Modules\Treasury\Models\ExpenseCategory;
use Modules\Treasury\Models\Payee;
use Modules\Treasury\Models\Transaction;

class PaymentController extends Controller
{
    public function create()
    {
        return Inertia::render('Treasury::Payments/Create', [
            'accounts' => Account::all(),
            'categories' => ExpenseCategory::all(),
            'payees' => Payee::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'payee_id' => 'nullable|exists:payees,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $path = null;
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
            }

            // 1. Create the transaction
            Transaction::create([
                'account_id' => $validated['account_id'],
                'expense_category_id' => $validated['expense_category_id'],
                'payee_id' => $validated['payee_id'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'type' => 'expense',
                'amount' => -$validated['amount'], // Store expenses as negative values
                'description' => $validated['description'],
                'attachment' => $path,
            ]);

            // 2. Update account balance
            $account = Account::find($validated['account_id']);
            $account->decrement('current_balance', $validated['amount']);
        });

        return redirect()->route('transactions.index')->with('success', 'پرداخت با موفقیت ثبت شد.');
    }
}
