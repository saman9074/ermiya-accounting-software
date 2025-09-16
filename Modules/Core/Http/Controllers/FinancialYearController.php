<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Core\Models\FinancialYear;
use Modules\Core\Models\Setting;

class FinancialYearController extends Controller
{
    public function index(Request $request)
    {
        $financialYears = FinancialYear::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // از خود جدول می‌خوانیم که کدام سال فعال است
        $activeYear = FinancialYear::where('is_active', true)->first();

        return Inertia::render('Core::FinancialYears/Index', [
            'financialYears' => $financialYears,
            'filters' => $request->only(['search']),
            'active_year' => $activeYear ? $activeYear->id : null,
        ]);
    }

    public function create()
    {
        return Inertia::render('Core::FinancialYears/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:financial_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $financialYear = FinancialYear::create($validated);
        if ($request->input('is_active')) {
            FinancialYear::setActive($financialYear);
        }
        return redirect()->route('financial-years.index')->with('success', 'سال مالی با موفقیت ایجاد شد.');
    }

    public function activate(FinancialYear $financialYear)
    {
        FinancialYear::setActive($financialYear);
        return redirect()->route('financial-years.index')->with('success', "سال مالی '{$financialYear->name}' با موفقیت فعال شد.");
    }

    public function edit(FinancialYear $financialYear)
    {
        return Inertia::render('Core::FinancialYears/Edit', [
            'financialYear' => $financialYear,
        ]);
    }

    public function update(Request $request, FinancialYear $financialYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:financial_years,name,' . $financialYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $financialYear->update($validated);

        return redirect()->route('financial-years.index')->with('success', 'سال مالی با موفقیت ویرایش شد.');
    }

    public function destroy(FinancialYear $financialYear)
    {
        if ($financialYear->is_active) {
            return redirect()->route('financial-years.index')->with('error', 'امکان حذف سال مالی فعال وجود ندارد.');
        }

        $financialYear->delete();
        return redirect()->route('financial-years.index')->with('success', 'سال مالی با موفقیت حذف شد.');
    }
}
