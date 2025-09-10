<?php

use Illuminate\Support\Facades\Route;
use Modules\Treasury\Http\Controllers\TreasuryController;

Route::middleware('auth')->group(function() {
    Route::resource('accounts', TreasuryController::class)->except(['show']);
});



