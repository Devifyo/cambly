<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UseCreditService;
use App\Services\{UserSubscriptionService, DashboardService};
use Illuminate\Support\Carbon;
class StudentDashboardController extends Controller
{   
    protected $creditService;
    protected UserSubscriptionService $subs;
    protected DashboardService $dashboardService;
    public function __construct(UseCreditService $creditService, UserSubscriptionService $subs, DashboardService $dashboardService)
    {
        $this->creditService = $creditService;
         $this->subs = $subs;
         $this->dashboardService = $dashboardService;
    }
        public function index()
    {   
        $user = auth()->user();
        $currentCredits = $this->creditService->getCurrentMonthCredits($user);
        if (
            isset($currentCredits['available'], $currentCredits['issued']) &&
            $currentCredits['issued'] > 0
        ) {
            $percentage = ($currentCredits['available'] / $currentCredits['issued']) * 100;
            $currentCredits['consume_percentage'] = number_format($percentage, 2);
        } else {
            $currentCredits['consume_percentage'] = '0';
        }
        $dashbordDetails = $this->dashboardService->getStudentDashboardData(auth()->user());
        // dd($dashbordDetails);
        $activeSubscription =  $this->subs->activeSubscription($user);
        return view('student.dashboard', [
            'currentCredits' => $currentCredits,
            'activeSubscription' => $activeSubscription,
            'dashboardDetails' => $dashbordDetails
        ]);
    }

    public function getCalendarEvents(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();
        // Validate the incoming 'date' query parameter
        $validated = $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);
            $range = [
                'start' => $request->start,
                'end'   => $request->end
            ];

            $date = intelligentMonthStart($range);
        // Get the date from the request, or default to today's date
        $date = $validated['date'] ?? Carbon::now()->toDateString();
    
        // Call your new service method
        $events = $this->dashboardService->getStudentReservationsForMonth($user, $date);
        // dd($events);
        // Return the data as JSON
        return response()->json($events);
    }
}
