<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteVacuna extends Model
{
    protected $table = 'pacientes_vacuna';
    protected $fillable = [
        'pacientes_id',
        'dosis_1',
        'dosis_2',
        'refuerzo',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }
}
