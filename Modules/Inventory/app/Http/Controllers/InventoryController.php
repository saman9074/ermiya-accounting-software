<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Unit;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load the 'unit' relationship to prevent N+1 query issues
        $products = Product::with('unit')->latest()->get();

        return Inertia::render('Inventory::Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pass all categories and units to the create form for dropdowns
        return Inertia::render('Inventory::Create', [
            'categories' => Category::all(),
            'units' => Unit::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:191|unique:products,sku',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'کالا با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Pass the product and all categories/units to the edit form
        return Inertia::render('Inventory::Edit', [
            'product' => $product,
            'categories' => Category::all(),
            'units' => Unit::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:191|unique:products,sku,' . $product->id,
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'کالا با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'کالا با موفقیت حذف شد.');
    }
}
