<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'teacher_id',
        'availability_id',
        'is_hold',
        'cycle_start_utc',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
}
