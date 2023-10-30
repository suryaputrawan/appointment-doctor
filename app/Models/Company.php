<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'whatsapp',
        'email',
        'instagram',
        'facebook',
        'logo',
        'favicon'
    ];

    public function getTakeLogoAttribute()
    {
        return "/storage/" . $this->logo;
    }

    public function getTakeFaviconAttribute()
    {
        return "/storage/" . $this->favicon;
    }

    public function doctorLocation(): HasMany
    {
        return $this->hasMany(DoctorLocation::class);
    }
}
