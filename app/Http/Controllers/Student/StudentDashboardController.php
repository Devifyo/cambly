<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UseCreditService;
use App\Services\{UserSubscriptionService, DashboardService};

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
        $activeSubscription =  $this->subs->getActiveSubscriptionDetails($user);
        return view('student.dashboard', [
            'currentCredits' => $currentCredits,
            'activeSubscription' => $activeSubscription,
            'dashboardDetails' => $dashbordDetails
        ]);
    }
}
