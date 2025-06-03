<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docter extends Model
{
    /** @use HasFactory<\Database\Factories\DocterFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'specialization'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function medical_records(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
