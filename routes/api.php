<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/me', [UserController::class, 'show']);
});