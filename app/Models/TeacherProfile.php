<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'preferred_name',
        'native_language',
        'english_level',
        'age',
        'tz',
        'discord_id',
        'bio',
        'started_at',
        'gender',
        'experience',
        'short_bio'
    ];

            public function user()
        {
            return $this->belongsTo(User::class);
        }
}
