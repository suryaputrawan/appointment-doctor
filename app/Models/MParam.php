<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MParam extends Model
{
    use HasFactory;

    protected $table = 'm_params';

    protected $fillable = [
        'auto_no_surat',
        'format_surat',
        'hospital_id',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }
}
