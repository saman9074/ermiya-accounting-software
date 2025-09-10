<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\SalesController;

Route::middleware('auth')->group(function() {
    Route::resource('invoices', SalesController::class);
});
