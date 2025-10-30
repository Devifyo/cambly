<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'cycle_number',
        'credits',
        'type',
        'reason',
        'reference',
        'description',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Check if a reference already exists to prevent duplicate transactions
     */
    public static function referenceExists(string $reference): bool
    {
        return static::where('reference', $reference)->exists();
    }

    /**
     * Scope to get transactions for a specific student and cycle
     */
    public function scopeForStudentCycle($query, int $studentId, int $cycleNumber)
    {
        return $query->where('student_id', $studentId)
                     ->where('cycle_number', $cycleNumber);
    }

    /**
     * Scope to get issued credits
     */
    public function scopeIssued($query)
    {
        return $query->where('type', 'issued');
    }

    /**
     * Scope to get used credits
     */
    public function scopeUsed($query)
    {
        return $query->where('type', 'used');
    }
}