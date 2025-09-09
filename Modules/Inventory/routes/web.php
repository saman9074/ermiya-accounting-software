<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\CategoryController;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Controllers\UnitController;

Route::middleware('auth')->group(function () {
    // This single line creates all the necessary routes for CRUD operations:
    // index, create, store, edit, update, destroy
    Route::resource('products', InventoryController::class)
        ->names('products') // Optional: sets a base name for routes
        ->except(['show']); // We don't need a dedicated 'show' page for now
    Route::resource('units', UnitController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
});
