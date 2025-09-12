<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesController;
use Modules\Sales\Http\Controllers\SalesReturnController;

Route::middleware('auth')->group(function() {
    Route::resource('invoices', SalesController::class);
    Route::get('invoices/{invoice}/return', [SalesReturnController::class, 'create'])->name('sales_returns.create');
    Route::post('sales_returns', [SalesReturnController::class, 'store'])->name('sales_returns.store');
    Route::get('sales_returns/{sales_return}', [SalesReturnController::class, 'show'])->name('sales_returns.show');
});
