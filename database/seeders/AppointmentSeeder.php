<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', 'user')->get();
        $services = Service::all();
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled'];

        // Criar agendamentos para os próximos 7 dias
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            
            // Pular se for domingo
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            foreach ($clients as $client) {
                // 50% de chance de criar um agendamento para cada cliente
                if (rand(0, 1)) {
                    $service = $services->random();
                    $status = $date->isPast() ? 'completed' : $statuses[array_rand(['scheduled', 'confirmed'])];
                    $hour = rand(9, 17);
                    $minute = rand(0, 1) * 30; // 00 ou 30

                    Appointment::create([
                        'user_id' => $client->id,
                        'service_id' => $service->id,
                        'appointment_time' => sprintf('%02d:%02d', $hour, $minute),
                        'date' => $date->format('Y-m-d'),
                        'status' => $status,
                        'notes' => 'Agendamento automático via seeder',
                    ]);
                }
            }
        }
    }
}