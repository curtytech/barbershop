<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentTime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarberController extends Controller
{
    public function show($slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        $employees = $store->employees()->with('services')->get();

        return view('barbers.show', compact('store', 'employees'));
    }

    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
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
        $appointment->employee_id = $validated['employee_id'];
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

    public function availability(Request $request)
    {
        $date = $request->query('date');
        $serviceId = $request->query('service_id');

        if (!$date || !$serviceId) {
            return response()->json([
                'available_times' => [],
            ]);
        }

        $service = \App\Models\Service::find($serviceId);
        if (!$service) {
            return response()->json(['available_times' => []]);
        }

        $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);

        // Check if service is available on this day
        // days_of_week is now an array (casted in model)
        if ($service->days_of_week && !in_array($dayOfWeek, $service->days_of_week)) {
            if (!$service->specific_date || $service->specific_date->format('Y-m-d') !== $date) {
                return response()->json(['available_times' => []]);
            }
        }

        if ($service->specific_date && $service->specific_date->format('Y-m-d') !== $date) {
            // Logic handled above or irrelevant if day_of_week matches
        }

        // Generate slots based on start_time, end_time, duration
        $startTime = Carbon::parse($date . ' ' . $service->start_time->format('H:i:s'));
        $endTime = Carbon::parse($date . ' ' . ($service->end_time ? $service->end_time->format('H:i:s') : '23:59:59'));
        
        // Fetch booked times for this service (and employee) on this date to avoid N+1 queries
        // Use whereDate to handle potential time components in the date column (e.g. SQLite storing Y-m-d H:i:s)
        $bookedTimes = Appointment::whereDate('date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($service) {
                // Check both employee (to prevent double booking person) and service (to prevent double booking specific service if needed)
                $query->where('employee_id', $service->employee_id)
                      ->orWhere('service_id', $service->id);
            })
            ->get() // Get collection to use model casting if needed, or raw data
            ->map(function ($appointment) {
                // Extract time from appointment_time (which might be a full datetime string)
                return \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i');
            })
            ->toArray();

        $slots = [];
        while ($startTime->copy()->addMinutes($service->duration)->lte($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($service->duration);
            $timeStr = $startTime->format('H:i');
            
            // Check breaks
            $isBreak = false;
            if ($service->break_start && $service->break_end) {
                $breakStart = Carbon::parse($date . ' ' . $service->break_start->format('H:i:s'));
                $breakEnd = Carbon::parse($date . ' ' . $service->break_end->format('H:i:s'));
                
                if ($startTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $isBreak = true;
                }
            }
            
            if (!$isBreak) {
                // Check if this specific time slot is in the booked times array
                $isBooked = in_array($timeStr, $bookedTimes);
                    
                $slots[] = [
                    'time' => $timeStr,
                    'available' => !$isBooked
                ];
            }
            
            $startTime->addMinutes($service->duration);
        }

        return response()->json([
            'available_times' => $slots,
        ]);
    }
}
