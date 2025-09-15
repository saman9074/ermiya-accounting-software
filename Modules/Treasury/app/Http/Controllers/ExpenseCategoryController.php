<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Treasury\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Treasury::ExpenseCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Treasury::ExpenseCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'دسته بندی هزینه با موفقیت ایجاد شد.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return Inertia::render('Treasury::ExpenseCategories/Edit', [
            'category' => $expenseCategory,
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')->with('success', 'دسته بندی هزینه با موفقیت ویرایش شد.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        // TODO: Add logic to check if category is in use before deleting
        $expenseCategory->delete();
        return redirect()->route('expense-categories.index')->with('success', 'دسته بندی هزینه با موفقیت حذف شد.');
    }
}
