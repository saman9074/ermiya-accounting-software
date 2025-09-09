<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // Eager load all descendants recursively and get only top-level categories
        $categories = Category::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        return Inertia::render('Inventory::Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory::Categories/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category)
    {
        // برای جلوگیری از انتخاب خود دسته‌بندی یا زیرشاخه‌هایش به عنوان والد
        $descendantIds = $this->getDescendantIds($category);
        $descendantIds[] = $category->id;

        return Inertia::render('Inventory::Categories/Edit', [
            'category' => $category,
            'categories' => Category::whereNotIn('id', $descendantIds)->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index');
    }

    // یک تابع کمکی برای پیدا کردن تمام زیرشاخه‌های یک دسته‌بندی
    private function getDescendantIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}
