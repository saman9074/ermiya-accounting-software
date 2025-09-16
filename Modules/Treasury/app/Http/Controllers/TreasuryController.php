<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Treasury\Models\Account;
use Modules\Core\Models\Currency;

class TreasuryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accounts = Account::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Treasury::Accounts/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Treasury::Accounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'type' => 'required|in:cash,bank',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        // تبدیل موجودی اولیه به ریال قبل از ذخیره
        if ($divisor > 1) {
            $validated['initial_balance'] = ($validated['initial_balance'] ?? 0) * $divisor;
        }

        // Set current_balance to initial_balance on creation
        $validated['current_balance'] = $validated['initial_balance'];

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'حساب جدید با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        return Inertia::render('Treasury::Accounts/Edit', [
            'account' => $account,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'type' => 'required|in:cash,bank',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $activeCurrency = Currency::where('is_active', true)->first();
        $divisor = $activeCurrency ? $activeCurrency->divisor : 1;

        // تبدیل موجودی اولیه به ریال قبل از به‌روزرسانی
        if ($divisor > 1) {
            $validated['initial_balance'] = ($validated['initial_balance'] ?? 0) * $divisor;
        }

        $balanceDifference = $validated['initial_balance'] - $account->initial_balance;
        $validated['current_balance'] = $account->current_balance + $balanceDifference;

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'حساب با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'حساب با موفقیت حذف شد.');
    }
}
