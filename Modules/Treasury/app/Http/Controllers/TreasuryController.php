<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Treasury\Models\Account;
class TreasuryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Treasury::Accounts/Index', [
            'accounts' => Account::all(),
            'success' => session('success'),
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
