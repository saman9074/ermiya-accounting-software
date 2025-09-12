<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\CategoryController;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Controllers\ProductKardexController;
use Modules\Inventory\Http\Controllers\UnitController;

Route::middleware('auth')->group(function () {
    Route::resource('products', InventoryController::class)->except(['show']);
    Route::resource('units', UnitController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Route for Product Kardex
    Route::get('products/{product}/kardex', ProductKardexController::class)->name('products.kardex');
});
