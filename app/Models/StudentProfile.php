<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_name',
        'date_of_birth',
        'tz',
        'native_language',
        'english_level',
        'discord_id',
        'joined_at',
        'country_residence'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];


    public function getAgeAttribute() {
    return $this->date_of_birth ? $this->date_of_birth->age : null;
}
}
