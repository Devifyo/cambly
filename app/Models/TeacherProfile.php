<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  TeacherProfile  extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'preferred_name',
        'native_language',
        'english_level',
        'date_of_birth',
        'tz',
        'discord_id',
        'zoom_link',
        'bio',
        'started_at',
        'gender',
        'experience',
        'short_bio',
        'country_residence',
        'games',
        'introduction',
        'youtube_url',
    ];
            protected $casts = [
        'date_of_birth' => 'date',
    ];
            public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function getAgeAttribute() {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
        }
}
