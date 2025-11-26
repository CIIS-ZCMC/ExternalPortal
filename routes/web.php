<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('portal.login');
});

Route::get("/portal/login", [AuthController::class, 'loginPage'])->name('portal.login');


Route::post("login", [AuthController::class, 'login'])->name('login');
