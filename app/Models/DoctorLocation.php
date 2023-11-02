<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorLocation extends Model
{
    use HasFactory;

    protected $table = 'doctor_locations';

    protected $fillable = [
        'doctor_id',
        'hospital_id'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctorLocationDay()
    {
        return $this->hasMany(DoctorLocationDay::class);
    }
}
