<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

Route::get('/barbers/{barber}/availability', [BarberController::class, 'availability'])->name('barber.availability');
Route::get('/{slug}', [BarberController::class, 'show'])->name('barber.show');
Route::post('/appointment/store', [BarberController::class, 'storeAppointment'])->name('appointment.store');

