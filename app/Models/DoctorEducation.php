<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorEducation extends Model
{
    use HasFactory;

    protected $table = 'doctor_educations';

    protected $fillable = [
        'doctor_id',
        'university_name',
        'specialization',
        'start_year',
        'end_year'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
