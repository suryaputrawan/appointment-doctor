<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorLocationDay extends Model
{
    use HasFactory;

    protected $table = 'doctor_location_days';

    protected $fillable = [
        'doctor_location_id',
        'day',
        'time'
    ];

    public function doctorLocation()
    {
        return $this->belongsTo(DoctorLocation::class);
    }
}