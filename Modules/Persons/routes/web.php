<?php

use Illuminate\Support\Facades\Route;
use Modules\Persons\Http\Controllers\PersonGroupController;
use Modules\Persons\Http\Controllers\PersonsController;


Route::middleware('auth')->group(function() {
    Route::resource('persons', PersonsController::class)->except(['show']);
    Route::resource('person-groups', PersonGroupController::class)->except(['show']);

    Route::get('persons/{person}/statement', [PersonsController::class, 'statement'])->name('persons.statement');
});
