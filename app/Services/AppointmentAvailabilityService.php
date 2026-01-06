<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentAvailabilityService
{
    public static function isAvailable(
        int $employeeId,
        string $date,
        string $time
    ): bool {

        $start = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}");
        $end   = $start->copy()->addHour();

        return ! Appointment::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->where(function ($query) use ($start, $end) {

                $query
                    // agendamento começa dentro do intervalo
                    ->whereBetween('appointment_time', [
                        $start->format('H:i:s'),
                        $end->format('H:i:s'),
                    ])
                    // ou começa antes mas invade o intervalo
                    ->orWhereRaw(
                        "time(appointment_time, '+1 hour') > ? 
                         AND appointment_time < ?",
                        [
                            $start->format('H:i:s'),
                            $start->format('H:i:s'),
                        ]
                    );
            })
            ->exists();
    }
}
