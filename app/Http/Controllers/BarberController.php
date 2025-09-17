<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function show($slug)
    {
        $barber = User::where('slug', $slug)
            ->where('role', 'barber')
            ->with([
                'services',
                'activeAppointmentTimes',
                'availableAppointmentTimes',
                'breakTimes',
                'upcomingAppointments.service',
                'todayAppointments.service'
            ])
            ->firstOrFail();

        return view('barbers.show', compact('barber'));
    }
}