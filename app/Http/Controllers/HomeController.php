<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentTime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function show()
    {

        $users = User::whereHas('stores')->with('stores')->get();

        // dump($users);

        return view('welcome', compact('users'));
    }
}
