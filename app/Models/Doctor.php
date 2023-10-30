<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';
    protected $fillable = [
        'name',
        'specialization',
        'speciality_id',
        'about_me',
        'picture'
    ];

    public function getTakePictureAttribute()
    {
        return "/storage/" . $this->picture;
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function doctorEducation(): HasMany
    {
        return $this->hasMany(DoctorEducation::class);
    }

    public function practiceSchedules(): HasMany
    {
        return $this->hasMany(PracticeSchedule::class);
    }

    public function doctorLocation(): HasMany
    {
        return $this->hasMany(DoctorLocation::class);
    }
}
