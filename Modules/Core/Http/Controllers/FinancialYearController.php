<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Core\Models\FinancialYear;

class FinancialYearController extends Controller
{
    public function index()
    {
        return Inertia::render('Core::FinancialYears/Index', [
            'financialYears' => FinancialYear::orderBy('start_date', 'desc')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Core::FinancialYears/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            if ($validated['is_active']) {
                // Deactivate all other years
                FinancialYear::where('is_active', true)->update(['is_active' => false]);
            }
            FinancialYear::create($validated);
        });

        return redirect()->route('financial-years.index')->with('success', 'سال مالی جدید با موفقیت ایجاد شد.');
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
            'name' => 'required|string|max:191',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($validated, $financialYear) {
            if ($validated['is_active']) {
                // Deactivate all other years
                FinancialYear::where('id', '!=', $financialYear->id)->where('is_active', true)->update(['is_active' => false]);
            }
            $financialYear->update($validated);
        });

        return redirect()->route('financial-years.index')->with('success', 'سال مالی با موفقیت ویرایش شد.');
    }

    public function destroy(FinancialYear $financialYear)
    {
        $financialYear->delete();
        return redirect()->route('financial-years.index')->with('success', 'سال مالی با موفقیت حذف شد.');
    }

    public function activate(FinancialYear $financialYear)
    {
        DB::transaction(function () use ($financialYear) {
            FinancialYear::where('is_active', true)->update(['is_active' => false]);
            $financialYear->update(['is_active' => true]);
        });

        return back()->with('success', "سال مالی '{$financialYear->name}' فعال شد.");
    }
}
