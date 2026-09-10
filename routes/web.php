<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;


Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->middleware('auth')->name('events.register');

Route::middleware('auth')->group(function () {
    Route::get('/registrations/{registration}/pay', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/registrations/{registration}/status', [PaymentController::class, 'status'])->name('payments.status');
});

// Webhook Midtrans - dikecualikan dari CSRF di bootstrap/app.php
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])->name('payments.notification');




Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
