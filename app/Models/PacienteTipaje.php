<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteTipaje extends Model
{
    protected $table = 'pacientes_tipaje';
    protected $fillable = [
        'pacientes_id',
        'madre',
        'padre',
        'sensibilidad',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }
}
