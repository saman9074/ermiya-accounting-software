<?php

use Illuminate\Support\Facades\Route;
use Modules\Persons\Http\Controllers\PersonsController;

//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::resource('persons', PersonsController::class)->names('persons');
//});

Route::middleware('auth')->group(function() {
    Route::resource('persons', PersonsController::class)->except(['show']);
});
