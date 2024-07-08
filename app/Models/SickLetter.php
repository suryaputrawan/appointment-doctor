<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SickLetter extends Model
{
    use HasFactory;

    protected $table = 'sick_letters';

    protected $fillable = [
        'slug',
        'nomor',
        'date',
        'patient_name',
        'patient_email',
        'age',
        'gender',
        'profession',
        'address',
        'start_date',
        'end_date',
        'diagnosis',
        'hospital_id',
        'created_by'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }
}
