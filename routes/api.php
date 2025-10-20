<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\StringAnalysisController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/me', [UserController::class, 'show']);
});

Route::prefix('strings')->controller(StringAnalysisController::class)->group(function () {
    // GET /api/strings - Filter strings
    Route::get('/',  'index')->name('strings.index');
    // POST /api/strings - Create and analyze a new string
    Route::post('/',  'store')->name('strings.store');
    // GET /api/strings/natural-language-filter - Filter strings with natural language query
    Route::get('/filter-by-natural-language',  'filterByNaturalLanguage')->name('strings.filterByNaturalLanguage');
    // GET /api/strings/{stringValue} - Get a string by its exact value
    Route::get('/{string_value}', 'show')->name('strings.show');
    // DELETE /api/strings/{stringValue} - Get a string by its exact value
    Route::delete('/{string_value}', 'destroy')->name('strings.delete');
});
