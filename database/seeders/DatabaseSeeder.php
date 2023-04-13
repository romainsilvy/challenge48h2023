<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Room;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'first_name' => 'romain',
            'last_name' => 'silvy',
            'email' => 'romain@silvy-leligois.fr',
            'password' => bcrypt('password'),
            'badge' => Str::uuid(),
        ]);

        for ($i=0; $i < 4; $i++) {
           for ($j=0; $j < 4; $j++) {
               Room::create([
                   'name' => 'Salle ' . ($i + 1) . '0' . ($j + 1),
                   'capacity' => 30,
               ]);
           }
        }
    }
}
