<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchLandingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberAuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, "index"]);
Route::get('/{slug}', [HomeController::class, "detail"]);

Route::get('/auth/google', [MemberAuthController::class, 'redirect'])->name('member.google.redirect');
Route::get('/auth/google/callback', [MemberAuthController::class, 'callback'])->name('member.google.callback');
Route::post('/logout', [MemberAuthController::class, 'logout'])->name('member.logout');


Route::middleware('auth:member')->group(function () {
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
});