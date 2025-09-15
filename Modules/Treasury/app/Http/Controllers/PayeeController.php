<?php
namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Treasury\Models\Payee;

class PayeeController extends Controller
{
    public function index(Request $request)
    {
        $payees = Payee::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Treasury::Payees/Index', [
            'payees' => $payees,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Treasury::Payees/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:payees,name']);
        Payee::create($validated);
        return redirect()->route('payees.index')->with('success', 'طرف حساب با موفقیت ایجاد شد.');
    }

    public function edit(Payee $payee)
    {
        return Inertia::render('Treasury::Payees/Edit', ['payee' => $payee]);
    }

    public function update(Request $request, Payee $payee)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:payees,name,' . $payee->id]);
        $payee->update($validated);
        return redirect()->route('payees.index')->with('success', 'طرف حساب با موفقیت ویرایش شد.');
    }

    public function destroy(Payee $payee)
    {
        $payee->delete();
        return redirect()->route('payees.index')->with('success', 'طرف حساب با موفقیت حذف شد.');
    }
}
