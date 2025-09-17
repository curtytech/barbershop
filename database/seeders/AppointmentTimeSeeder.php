<?php

namespace Database\Seeders;

use App\Models\AppointmentTime;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentTimeSeeder extends Seeder
{
    public function run(): void
    {
        $barbers = User::where('role', 'barber')->take(3)->get();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        foreach ($barbers as $barber) {
            foreach ($daysOfWeek as $day) {
                // Horário de trabalho regular
                AppointmentTime::create([
                    'user_id' => $barber->id,
                    'start_time' => '09:00',
                    'end_time' => '12:00',
                    'day_of_week' => $day,
                    'type' => 'available',
                    'duration' => 30,
                    'is_active' => true,
                ]);

                // Horário de almoço
                AppointmentTime::create([
                    'user_id' => $barber->id,
                    'start_time' => '12:00',
                    'end_time' => '13:00',
                    'day_of_week' => $day,
                    'type' => 'lunch',
                    'is_active' => true,
                ]);

                // Horário da tarde
                AppointmentTime::create([
                    'user_id' => $barber->id,
                    'start_time' => '13:00',
                    'end_time' => '18:00',
                    'day_of_week' => $day,
                    'type' => 'available',
                    'duration' => 30,
                    'is_active' => true,
                ]);
            }
        }
    }
}