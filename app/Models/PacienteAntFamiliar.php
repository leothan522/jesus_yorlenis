<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteAntFamiliar extends Model
{
    protected $table = 'pacientes_ant_familiares';
    protected $fillable = [
        'pacientes_id',
        'antecedentes_id',
        'is_bool',
        'texto',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }
}
