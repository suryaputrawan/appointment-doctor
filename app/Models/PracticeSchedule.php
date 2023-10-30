<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeSchedule extends Model
{
    use HasFactory;

    protected $table = 'practice_schedules';

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'booking_status'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
