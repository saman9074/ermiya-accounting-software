<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\PriceList;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Unit;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        return Inertia::render('Inventory::Create', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'priceLists' => PriceList::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:191|unique:products,sku',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0', // Main sale price is required
            'stock' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
            'prices' => 'nullable|array',
            'prices.*.price_list_id' => 'required|exists:price_lists,id',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if (!empty($validated['prices'])) {
                foreach ($validated['prices'] as $priceData) {
                    $product->productPrices()->create([
                        'price_list_id' => $priceData['price_list_id'],
                        'price' => $priceData['price'],
                    ]);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'کالا با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Eager load the relationships to be sent to the frontend
        $product->load('productPrices');

        return Inertia::render('Inventory::Edit', [
            'product' => $product,
            'categories' => Category::all(),
            'units' => Unit::all(),
            'priceLists' => PriceList::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'nullable|string|max:191|unique:products,sku,' . $product->id,
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
            'prices' => 'nullable|array',
            'prices.*.price_list_id' => 'required|exists:price_lists,id',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update($validated);

            // Sync product prices
            $product->productPrices()->delete(); // Remove old prices
            if (!empty($validated['prices'])) {
                foreach ($validated['prices'] as $priceData) {
                    $product->productPrices()->create([
                        'price_list_id' => $priceData['price_list_id'],
                        'price' => $priceData['price'],
                    ]);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'کالا با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'کالا با موفقیت حذف شد.');
    }
}

