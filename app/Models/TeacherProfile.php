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
        'age',
        'tz',
        'discord_id',
        'bio',
        'started_at'
    ];
}
