<?php

namespace Database\Seeders;

use App\Models\Vacuna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $antecedentes = [
            [
                'nombre' => 'Toxoide',
            ],
            [
                'nombre' => 'Hepatitis',
            ],
            [
                'nombre' => 'COVID',
            ],
        ];
        foreach ($antecedentes as $antecedente){
            $table = new Vacuna();
            $table->nombre = $antecedente['nombre'];
            $table->save();
        }
    }
}
