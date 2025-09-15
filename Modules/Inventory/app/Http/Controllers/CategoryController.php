<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // دریافت همه دسته‌بندی‌ها و ساختن ساختار درختی
        $categories = Category::query()
            ->when($request->input('search'), function ($query, $search) {
                // جستجو در والد و فرزندان
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('parent', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->with('parent')
            // دریافت والدترین دسته‌بندی‌ها (آنهایی که parent_id ندارند) به همراه فرزندانشان
            ->whereNull('parent_id')
            ->with('childrenRecursive') // لود کردن تمام زیرمجموعه‌ها به صورت بازگشتی
            ->get();


        return Inertia::render('Inventory::Categories/Index', [
            // ما دیگر از paginate استفاده نمی‌کنیم چون ساختار درختی است
            // برای دسته‌بندی‌ها که تعدادشان معمولا کم است، این روش بهتر است.
            'categories' => $categories,
            'filters' => $request->only(['search']),
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
