<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'start_utc',
        'end_utc',
        'is_booked'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}
