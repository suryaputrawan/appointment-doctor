<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeScheduleTime extends Model
{
    use HasFactory;

    protected $table = 'practice_schedule_times';

    protected $fillable = [
        'practice_schedule_id',
        'start_time',
        'end_time',
        'booking_status'
    ];

    public function practiceSchedule(): BelongsTo
    {
        return $this->belongsTo(PracticeSchedule::class);
    }
}
