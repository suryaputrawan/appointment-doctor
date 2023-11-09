<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory;

    protected $table = 'hospitals';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'whatsapp',
        'email',
        'instagram',
        'facebook',
        'logo',
    ];

    public function getTakeLogoAttribute()
    {
        return "/storage/" . $this->logo;
    }

    public function doctorLocation(): HasMany
    {
        return $this->hasMany(DoctorLocation::class);
    }

    public function practiceSchedule(): HasMany
    {
        return $this->hasMany(PracticeSchedule::class);
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
