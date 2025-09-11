<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\DashboardController;
use Modules\Core\Http\Controllers\FinancialYearController;
use Modules\Core\Http\Controllers\SettingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes for Financial Year Management
    Route::resource('financial-years', FinancialYearController::class)->except(['show']);
    Route::patch('financial-years/{financial_year}/activate', [FinancialYearController::class, 'activate'])->name('financial-years.activate');

    // Company Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');
});
