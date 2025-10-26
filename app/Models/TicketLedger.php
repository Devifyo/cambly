<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketLedger extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'cycle_number',
        'issued_credits',
        'used_credits',
        'hold_credits'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
