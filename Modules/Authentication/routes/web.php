<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\App\Http\Controllers\AuthenticationController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticationController::class, 'getLoginPage'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login.attempt');
});
