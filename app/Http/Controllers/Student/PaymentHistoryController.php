<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{WebhookEvent,User};
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    /**
     * Display the student's paginated payment history.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Validate the filters
        $filters = $request->validate([
            'q'    => 'nullable|string|max:100',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        // 2. Get the all-time stats (unfiltered)
        $stats = $this->calculateStats($user);

        // 3. Get the paginated AND filtered list of payments
        $payments = WebhookEvent::where('user_id', $user->id)
            ->where('type', 'invoice.paid')
            ->latest('created_at')
            ->applyFilters($filters)
            ->paginate(10)
            ->withQueryString();
        // 4. Return the view
        return view('student.inner.account.payment-history', compact('payments', 'stats'));
    }

    /**
     * Calculate all-time stats for the user.
     *
     * @param int $userId
     * @return array
     */
    private function calculateStats( $user): array
    {   
        $allEvents = WebhookEvent::where('user_id', $user->id)
            ->where('type', 'invoice.paid')
            ->get();
        
        $totalBilledAmount = 0;
        $currency = 'jpy'; // Default
        $plan = 'No Active Plan';
        
        $latestEvent = $allEvents->first();
        if ($latestEvent) {
             // We can use our new accessor here!
             $plan = $latestEvent->invoice_description;
        }

        foreach ($allEvents as $event) {
            // 1. Get the payload (Laravel casts it once, returning a JSON string)
            $payload = $event->payload;

            // 2. If it is still a string, decode it again manually
            if (is_string($payload)) {
                $payload = json_decode($payload, true);
            }

            // 3. Now it is a real array
            $invoice = $payload['data']['object']; // This will work now
            $currency = strtolower($invoice['currency']);
            
            // Call the public static method on the model
            $totalBilledAmount += WebhookEvent::formatStripeAmount(
                $invoice['amount_paid'],
                $currency
            );
        }
        return [
            'plan'         => $plan,
            'total_billed' => WebhookEvent::getCurrencySymbol($currency) . number_format($totalBilledAmount, 2),
            'next_billing' => 'N/A',
        ];
    }
}