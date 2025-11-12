<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        'ticket_ledger_id',
        'action_id',
        'action_type',
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

    /**
     * Apply dynamic filters to the credit transaction query.
     *
     * @param Builder $query   The Eloquent query builder instance.
     * @param array   $filters An array of validated filters from the request.
     * @return Builder
     */
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        // A) Apply the 'q' (search) filter
        // We use data_get to safely get the 'q' value.
        // The `when` closure will only run if $filters['q'] is present and not empty.
        $query->when(data_get($filters, 'q'), function ($q, $searchTerm) {
            // Group the 'or' statements to avoid interfering with other `where` clauses
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('description', 'like', '%' . $searchTerm . '%')
                         ->orWhere('reference', 'like', '%' . $searchTerm . '%')
                         ->orWhere('reason', 'like', '%' . $searchTerm . '%');
            });
        });

        // B) Apply the 'date' filter
        $query->when(data_get($filters, 'date'), function ($q, $date) {
            $q->whereDate('created_at', $date);
        });

        // Always return the query builder to allow for chaining
        return $query;
    }
}