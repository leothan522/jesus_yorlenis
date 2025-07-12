<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacuna extends Model
{
    use SoftDeletes;
    protected $table = 'vacunas';
    protected $fillable = [
        'nombre',
        'estatus'
    ];
}
