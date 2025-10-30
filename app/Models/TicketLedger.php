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
        'hold_credits',
        'stripe_invoice_id',
        'stripe_subscription_id'

    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
