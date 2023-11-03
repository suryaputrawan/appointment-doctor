<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeSchedule extends Model
{
    use HasFactory;

    protected $table = 'practice_schedules';

    protected $fillable = [
        'doctor_id',
        'hospital_id',
        'date',
        'start_time',
        'end_time',
        'booking_status'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function practiceScheduleTime(): HasMany
    {
        return $this->hasMany(PracticeScheduleTime::class);
    }
}
