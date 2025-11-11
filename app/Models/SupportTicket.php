<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone_number',
        'subject',
        'message',
        'status',
    ];

    /**
     * Get the user who submitted the ticket (if they were logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}