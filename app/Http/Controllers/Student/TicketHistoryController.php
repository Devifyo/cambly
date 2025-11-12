<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;

class TicketHistoryController extends Controller
{
    /**
     * Display the student's ticket history, with stats and filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Validate the filters and store them in an array
        $filters = $request->validate([
            'q'    => 'nullable|string|max:100',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $studentId = auth()->id();

        // --- 2. Calculate Stats ---
        // This part is unchanged. It gets *all* transactions for accurate stats.
        $allTransactions = CreditTransaction::where('student_id', $studentId)->get();

        $totalIssued = $allTransactions->where('type', 'issued')->sum('credits');
        $totalRefunded = $allTransactions->where('type', 'refund')->sum('credits');
        $totalUsed = $allTransactions->where('type', 'debt')->sum('credits');
        $totalHeld = $allTransactions->where('type', 'hold')->sum('credits');

        $stats = [
            'earned'    => $totalIssued,
            'used'      => $totalUsed - $totalRefunded,
            'remaining' => $totalIssued - ($totalUsed - $totalRefunded) - $totalHeld,
        ];

        // --- 3. Build the Filtered & Paginated Transaction List ---
        
        // This is now beautifully clean!
        $ticketHistory = CreditTransaction::where('student_id', $studentId)
                            ->latest()
                            ->applyFilters($filters)  // <-- Here is your new scope!
                            ->paginate(10)            // Show 10 per page
                            ->withQueryString();      // Keep filters in pagination links

        // --- 4. Return the View ---
        return view('student.inner.account.ticket-history', compact('ticketHistory', 'stats'));
    }
}