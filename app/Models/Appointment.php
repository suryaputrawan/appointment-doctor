<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'booking_number',
        'date',
        'start_time',
        'end_time',
        'hospital_id',
        'doctor_id',
        'patient_name',
        'patient_dob',
        'patient_sex',
        'patient_address',
        'patient_email',
        'patient_telp',
        'status'
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
