<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteAntOtro extends Model
{
    protected $table = 'pacientes_ant_otros';
    protected $fillable = [
        'pacientes_id',
        'antecedentes_id',
        'texto',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }
}
