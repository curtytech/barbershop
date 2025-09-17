<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário admin
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@barbershow.com',
            'slug' => Str::slug('Administrador'),
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // Criar alguns barbeiros
        $barbers = [
            ['name' => 'João Silva', 'email' => 'joao@barbershow.com', 'slug' => 'joao-silva'],
            ['name' => 'Pedro Santos', 'email' => 'pedro@barbershow.com', 'slug' => 'pedro-santos'],
            ['name' => 'Carlos Oliveira', 'email' => 'carlos@barbershow.com', 'slug' => 'carlos-oliveira'],
        ];

        foreach ($barbers as $barber) {
            User::factory()->create([
                'name' => $barber['name'],
                'email' => $barber['email'],
                'slug' => Str::slug($barber['name']),
                'password' => Hash::make('12345678'),
                'role' => 'barber',
            ]);
        }

        // Criar alguns clientes
        User::factory()->state([
            'password' => Hash::make('12345678'),
        ])->count(5)->create([
            'role' => 'user',
        ]);
    }
}