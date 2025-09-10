<?php

use Illuminate\Support\Facades\Route;
use Modules\Treasury\Http\Controllers\TransactionController;
use Modules\Treasury\Http\Controllers\TreasuryController;

Route::middleware('auth')->group(function() {
    // Routes for managing accounts (cash/bank)
    Route::resource('accounts', TreasuryController::class)->except(['show']);

    // Route for storing a new payment for an invoice
    Route::post('invoices/transactions', [TransactionController::class, 'store'])->name('invoices.transactions.store');

    // Routes for managing transactions
    Route::resource('transactions', TransactionController::class)->only(['index', 'edit', 'update', 'destroy']);
});



