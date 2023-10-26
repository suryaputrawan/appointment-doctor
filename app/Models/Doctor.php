<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function doctorEducation()
    {
        return $this->hasMany(DoctorEducation::class);
    }
}
