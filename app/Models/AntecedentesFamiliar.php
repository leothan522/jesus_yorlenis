<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntecedentesFamiliar extends Model
{
    use SoftDeletes;
    protected $table = 'antecedentes_familiares';
    protected $fillable = [
        'nombre',
        'is_bool',
        'estatus',
    ];

    public function pacienteAntFamiliares(): HasMany
    {
        return $this->hasMany(PacienteAntFamiliar::class, 'antecedentes_id', 'id');
    }
}
