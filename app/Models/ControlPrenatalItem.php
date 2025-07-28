<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlPrenatalItem extends Model
{
    protected $table = 'control_prenatal_items';
    protected $fillable = [
        'control_id',
        'fecha',
        'edad_gestacional',
        'peso',
        'ta',
        'au',
        'pres',
        'fcf',
        'mov_fetales',
        'du',
        'edema',
        'sintomas',
        'observaciones',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlPrenatal::class, 'control_id', 'id');
    }

}
