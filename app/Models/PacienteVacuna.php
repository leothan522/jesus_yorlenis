<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteVacuna extends Model
{
    protected $table = 'pacientes_vacunas';
    protected $fillable = [
        'pacientes_id',
        'vacunas_id',
        'dosis_1',
        'dosis_2',
        'refuerzo',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }

    public function vacuna(): BelongsTo
    {
        return $this->belongsTo(Vacuna::class, 'vacunas_id', 'id');
    }
}
