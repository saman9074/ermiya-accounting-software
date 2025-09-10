<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\DashboardController;
use Modules\Core\Http\Controllers\FinancialYearController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes for Financial Year Management
    Route::resource('financial-years', FinancialYearController::class)->except(['show']);
    Route::patch('financial-years/{financial_year}/activate', [FinancialYearController::class, 'activate'])->name('financial-years.activate');
});
