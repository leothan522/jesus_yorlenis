<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';
    protected $fillable = [
        'cedula',
        'nombre',
        'fecha_nacimiento',
        'edad',
        'telefono',
        'direccion',
        'fur',
        'fpp',
        'gestas',
        'partos',
        'cesareas',
        'abortos',
    ];
}
