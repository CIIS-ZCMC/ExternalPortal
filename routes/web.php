<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return redirect()->route('portal.login');
});

Route::get("/portal/login", [AuthController::class, 'loginPage'])->name('portal.login');

Route::get("/portal/register", [AuthController::class, 'registerPage'])->name('portal.register');
Route::get("login", fn() => redirect()->route('portal.login'))->name('login');
Route::post("login", [AuthController::class, 'login'])->name('login.submit');
Route::post("register", [AuthController::class, 'register'])->name('register');

Route::get("/portal/forgot-password", [AuthController::class, 'forgotPasswordPage'])->name('portal.forgotPassword');
Route::post("/portal/forgot-password", [MailController::class, 'sendResetPassword'])->name('portal.forgotPassword.submit');
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

Route::get('/dtr/download/{token}', function ($token) {
    $payload = Cache::get('dtr_download_' . $token);

    if (!$payload) {
        abort(403, 'Invalid or expired request.');
    }

    $biometric_id = $payload['biometric_id'] ?? null;
    $year = $payload['year'] ?? null;
    $month = $payload['month'] ?? null;

    if (!$biometric_id || !$year || !$month) {
        abort(403, 'Invalid request.');
    }

    $url = config('app.dtr_api_url') . "/api/dtr/download/{$biometric_id}/{$year}/{$month}?token=" . \App\Helpers\DtrToken::generate();

    $response = Http::get($url);

    if (!$response->successful()) {
        abort(404, 'DTR report not found.');
    }

    return response($response->body(), 200, [
        'Content-Type' => $response->header('Content-Type') ?? 'application/octet-stream',
    ]);
})->name('dtr.download');

Route::get('/test-email', function () {
    $mail = new \App\Http\Mailer\MailConfig();

    $result = $mail->send([
        'To_receiver' => 'reenjie17@gmail.com',
        'Receiver_Name' => 'Test User',
        'Subject' => 'Test Email from External Portal',
        'Body' => '<h3>Hello!</h3><p>This is a test email from the ZCMC External Portal.</p>',
    ]);

    if ($result === true) {
        return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
    }

    return response()->json(['success' => false, 'message' => 'Email failed to send.', 'error' => $result]);
});
