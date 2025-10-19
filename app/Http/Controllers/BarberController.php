<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentTime;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
    
    public function storeAppointment(Request $request)
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
        $appointment->appointment_time = $appointmentTime;
        $appointment->date = $validated['date'];
        $appointment->status = 'scheduled';
        $appointment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Agendamento realizado com sucesso!'
        ]);
    }
    
    public function availability(User $barber, Request $request)
    {
        $date = $request->query('date');
        $serviceId = $request->query('service_id');

        if (!$date) {
            return response()->json([
                'available_times' => [],
            ]);
        }

        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);

        $availableSlots = AppointmentTime::query()
            ->where('user_id', $barber->id)
            ->active()
            ->available()
            ->where(function ($q) use ($date, $dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)
                  ->orWhere('specific_date', $date);
            })
            ->get();

        $breaks = AppointmentTime::query()
            ->where('user_id', $barber->id)
            ->active()
            ->breaks()
            ->where(function ($q) use ($date, $dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)
                  ->orWhere('specific_date', $date);
            })
            ->get();

        $existingAppointments = Appointment::query()
            ->forUser($barber->id)
            ->forDate($date)
            ->get()
            ->map(function ($a) {
                if ($a->appointment_time instanceof \Carbon\Carbon) {
                    return $a->appointment_time->format('H:i');
                }
                return \Carbon\Carbon::parse($a->appointment_time)->format('H:i');
            })
            ->toArray();

        $breakIntervals = $breaks->map(function ($b) {
            $start = $b->start_time instanceof \Carbon\Carbon ? $b->start_time->copy() : \Carbon\Carbon::parse($b->start_time);
            $end = $b->end_time instanceof \Carbon\Carbon ? $b->end_time->copy() : \Carbon\Carbon::parse($b->end_time);
            return [$start, $end];
        })->toArray();

        $availableTimes = [];

        foreach ($availableSlots as $slot) {
            $duration = $slot->duration ?: 30;

            $start = $slot->start_time instanceof \Carbon\Carbon ? $slot->start_time->copy() : \Carbon\Carbon::parse($slot->start_time);
            $end = $slot->end_time instanceof \Carbon\Carbon ? $slot->end_time->copy() : \Carbon\Carbon::parse($slot->end_time);

            $cursor = $start->copy();
            while ($cursor->lt($end)) {
                $timeStr = $cursor->format('H:i');

                if (in_array($timeStr, $existingAppointments, true)) {
                    $cursor->addMinutes($duration);
                    continue;
                }

                $inBreak = false;
                foreach ($breakIntervals as [$bStart, $bEnd]) {
                    if ($cursor->gte($bStart) && $cursor->lt($bEnd)) {
                        $inBreak = true;
                        break;
                    }
                }
                if ($inBreak) {
                    $cursor->addMinutes($duration);
                    continue;
                }

                $availableTimes[] = $timeStr;
                $cursor->addMinutes($duration);
            }
        }

        $availableTimes = array_values(array_unique($availableTimes));
        sort($availableTimes);

        return response()->json([
            'available_times' => $availableTimes,
        ]);
    }
}