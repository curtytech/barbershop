<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $barbers = User::where('role', 'barber')->take(3)->get();

        $services = [
            [
                'name' => 'Corte de Cabelo',
                'description' => 'Corte de cabelo masculino com técnicas modernas',
                'price' => 50.00,
                'image' => 'services/corte.webp'
            ],
            [
                'name' => 'Barba',
                'description' => 'Modelagem e aparação de barba com produtos premium',
                'price' => 35.00,
                'image' => 'services/beard.svg'
            ],
            [
                'name' => 'Corte e Barba',
                'description' => 'Combo de corte de cabelo e barba com desconto especial',
                'price' => 75.00,
                'image' => 'services/haircut-beard.svg'
            ],
            [
                'name' => 'Hidratação',
                'description' => 'Tratamento de hidratação profunda para cabelos',
                'price' => 45.00,
                'image' => 'services/hydration.svg'
            ],
        ];

        foreach ($barbers as $barber) {
            foreach ($services as $service) {
                Service::create([
                    'user_id' => $barber->id,
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'price' => $service['price'],
                    'image' => $service['image'],
                ]);
            }
        }
    }
}