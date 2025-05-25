<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'docter_id',
        'patient_id',
        'date',
        'diagnosis',
        'treatment'
    ];


    public function docter(): BelongsTo
    {
        return $this->belongsTo(Docter::class, 'docter_id', 'id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    protected $casts = [
        'date' => 'datetime'
    ];
}
