<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;

    protected $table = 'specialities';
    protected $fillable = [
        'name',
        'picture',
    ];

    public function getTakePictureAttribute()
    {
        return "/storage/" . $this->picture;
    }

    public function doctor()
    {
        return $this->hasMany(Doctor::class);
    }
}
