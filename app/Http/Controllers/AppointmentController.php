<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function create()
    {
        $barbers = User::where('role', 'barber')->get();
        $services = Service::all();
        
        return view('appointments.create', compact('barbers', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
        ]);
        
        // Criar o horário do agendamento usando Carbon
        $appointmentTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        
        // Criar o agendamento
        $appointment = new Appointment();
        $appointment->user_id = $validated['barber_id'];
        $appointment->service_id = $validated['service_id'];
        $appointment->client_name = $validated['client_name'];
        $appointment->client_phone = $validated['client_phone'];
        $appointment->appointment_time = $appointmentTime->format('H:i:s');
        $appointment->date = $validated['date'];
        $appointment->status = 'scheduled';
        $appointment->save();
        
        return redirect()->route('appointments.success')->with('success', 'Agendamento realizado com sucesso!');
    }
    
    public function success()
    {
        return view('appointments.success');
    }
}