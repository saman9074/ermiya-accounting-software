<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesController;
use Modules\Sales\Http\Controllers\SalesReturnController;

Route::middleware('auth')->group(function() {
    Route::resource('invoices', SalesController::class);
    Route::get('invoices/{invoice}/return', [SalesReturnController::class, 'create'])->name('sales_returns.create_from_invoice');

    Route::resource('sales_returns', SalesReturnController::class)->except(['edit', 'update']);
});
