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
                'image' => 'https://cdn.pixabay.com/photo/2016/11/29/06/46/adult-1867889_1280.jpg'
            ],
            [
                'name' => 'Barba',
                'description' => 'Modelagem e aparação de barba com produtos premium',
                'price' => 35.00,
                'image' => 'https://cdn.pixabay.com/photo/2017/08/07/12/49/people-2603521_1280.jpg'
            ],
            [
                'name' => 'Corte e Barba',
                'description' => 'Combo de corte de cabelo e barba com desconto especial',
                'price' => 75.00,
                'image' => 'https://cdn.pixabay.com/photo/2017/07/18/17/16/black-2516434_1280.jpg'
            ],
            [
                'name' => 'Hidratação',
                'description' => 'Tratamento de hidratação profunda para cabelos',
                'price' => 45.00,
                'image' => 'https://cdn.pixabay.com/photo/2016/11/23/14/37/blur-1853262_1280.jpg'
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