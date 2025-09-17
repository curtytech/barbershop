<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{slug}', [BarberController::class, 'show'])->name('barber.show');

