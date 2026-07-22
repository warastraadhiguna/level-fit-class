<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchLandingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberCheckInController;
use App\Livewire\ClassCheckIn;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, "index"]);

Route::get('/admin/check-in', ClassCheckIn::class)
    ->middleware(FilamentAuthenticate::class)
    ->name('admin.check-in');

Route::get('/auth/google', [MemberAuthController::class, 'redirect'])->name('member.google.redirect');
Route::get('/auth/google/callback', [MemberAuthController::class, 'callback'])->name('member.google.callback');
Route::post('/logout', [MemberAuthController::class, 'logout'])->name('member.logout');


Route::middleware('auth:member')->group(function () {
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/member/check-in', [MemberCheckInController::class, 'scanner'])->name('member.check-in.scanner');
    Route::post('/member/check-in', [MemberCheckInController::class, 'toggle'])->name('member.check-in.toggle');
    Route::get('/member/trainer-session-check-in', [MemberCheckInController::class, 'trainerSessionScanner'])->name('member.trainer-session-check-in.scanner');
    Route::post('/member/trainer-session-check-in', [MemberCheckInController::class, 'toggleTrainerSession'])->name('member.trainer-session-check-in.toggle');
});

Route::get('/{slug}', [HomeController::class, "detail"]);
