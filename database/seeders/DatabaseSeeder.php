<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Yonathan Castillo',
            'email' => 'leothan522@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('20025623'),
            'is_root' => 1
        ]);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@morros-devops.xyz',
            'email_verified_at' => now(),
            'password' => Hash::make('admin1234'),
            'is_admin' => 1
        ]);
    }
}
