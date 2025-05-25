<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{

    protected $fillable = [
        'docter_id',
        'patient_id',
        'date',
        'status',
        'notes',
    ];

    public function docter(): BelongsTo
    {
        return $this->belongsTo(Docter::class, 'docter_id', 'id');
    }
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
}
