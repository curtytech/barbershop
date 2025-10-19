<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            ],
            [
                'name' => 'Barba',
                'description' => 'Modelagem e aparação de barba com produtos premium',
                'price' => 35.00,
            ],
            [
                'name' => 'Corte e Barba',
                'description' => 'Combo de corte de cabelo e barba com desconto especial',
                'price' => 75.00,
            ],
            [
                'name' => 'Hidratação',
                'description' => 'Tratamento de hidratação profunda para cabelos',
                'price' => 45.00,
            ],
        ];

        $imageMap = [
            'Corte de Cabelo' => 'corte.webp',
            'Barba' => 'beard.svg',
            'Corte e Barba' => 'haircut-beard.svg',
            'Hidratação' => 'hydration.svg',
        ];

        foreach ($barbers as $barber) {
            foreach ($services as $service) {
                $filename = $imageMap[$service['name']] ?? 'haircut.svg';
                $source = public_path('services/' . $filename);
                $target = 'services/' . $filename;

                // Copia a imagem para o disco 'public' se ainda não existir
                if (File::exists($source) && !Storage::disk('public')->exists($target)) {
                    Storage::disk('public')->put($target, File::get($source));
                }

                Service::create([
                    'user_id' => $barber->id,
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'price' => $service['price'],
                    'image' => $target, // Ex.: services/corte.webp
                ]);
            }
        }
    }
}