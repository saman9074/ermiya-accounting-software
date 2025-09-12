<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Inventory\Models\Product;

class ProductKardexController extends Controller
{
    public function __invoke(Product $product)
    {
        return Inertia::render('Inventory::Kardex', [
            'product' => $product,
            'movements' => $product->stockMovements()->with('reference')->get(),
        ]);
    }
}
