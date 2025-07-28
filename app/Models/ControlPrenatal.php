<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlPrenatal extends Model
{
    use SoftDeletes;
    protected $table = 'control_prenatal';
    protected $fillable = [
        'codigo',
        'pacientes_id'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ControlPrenatalItem::class, 'control_id', 'id');
    }

}
