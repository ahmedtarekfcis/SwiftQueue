<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('appointments/create');
});

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::post('/appointments', [\App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/appointments/status', [\App\Http\Controllers\AppointmentController::class, 'status']);
Route::get('/appointments/create', [\App\Http\Controllers\AppointmentController::class, 'create']);

Route::prefix('admin/appointments')->name('admin.appointments.')->group(function () {
    Route::get('/queue', [\App\Http\Controllers\Admin\AppointmentController::class, 'queue'])->name('queue');
    Route::post('/call-next', [\App\Http\Controllers\Admin\AppointmentController::class, 'callNext'])->name('callNext');
    Route::post('/admin/appointments/return-previous', [\App\Http\Controllers\Admin\AppointmentController::class, 'returnPrevious'])
        ->name('returnPrevious');
    Route::post('/pause-booking', [\App\Http\Controllers\Admin\AppointmentController::class, 'pauseBooking'])->name('pauseBooking');
    Route::post('/resume-booking', [\App\Http\Controllers\Admin\AppointmentController::class, 'resumeBooking'])->name('resumeBooking');
})->middleware(['auth', 'admin']);
