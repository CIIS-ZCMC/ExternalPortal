<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return redirect()->route('portal.login');
});

Route::get("/portal/login", [AuthController::class, 'loginPage'])->name('portal.login');

Route::get("/portal/register", [AuthController::class, 'registerPage'])->name('portal.register');
Route::post("login", [AuthController::class, 'login'])->name('login');
Route::post("register", [AuthController::class, 'register'])->name('register');

Route::get("/portal/forgot-password", [AuthController::class, 'forgotPasswordPage'])->name('portal.forgotPassword');
Route::post("/portal/forgot-password", [MailController::class, 'sendResetPassword'])->name('portal.forgotPassword');
Route::get("/portal/reset-password", [AuthController::class, 'resetPasswordPage'])->name('portal.resetPassword');

Route::post("/portal/save-password", [AuthController::class, 'savePassword'])->name('portal.savePassword');

Route::get("/sendConfirmation", [MailController::class, 'sendConfirmation'])->name('portal.sendConfirmation');

Route::get("/activate", [AuthController::class, 'activate'])->name('portal.activate');

Route::get("/checkEmail", [AuthController::class, 'checkEmail'])->name('portal.checkEmail');

Route::get("/expire", [AuthController::class, 'expire'])->name('portal.expire');
Route::get("/AccountActivated", [AuthController::class, 'AccountActivated'])->name('portal.AccountActivated');

Route::get('/successful', [AuthController::class, 'successful'])->name('portal.successful');

Route::get("/admin/login", [AuthController::class, 'adminLogin'])->name('admin.login');
Route::post("/admin/signin", [AuthController::class, 'AdminSignin'])->name('admin.signin');

Route::controller(GoogleController::class)->group(function () {
    Route::get("/auth/google", "redirectToGoogle")->name("auth.google");
    Route::get("/auth/google/callback", "handleGoogleCallback")->name("auth.google.callback");
    Route::get("/notFound", "notFound")->name("google.notFound");
});
