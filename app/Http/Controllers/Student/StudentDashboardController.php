<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UseCreditService;
use App\Services\UserSubscriptionService;

class StudentDashboardController extends Controller
{   
    protected $creditService;
    protected UserSubscriptionService $subs;
    public function __construct(UseCreditService $creditService, UserSubscriptionService $subs)
    {
        $this->creditService = $creditService;
         $this->subs = $subs;
    }
        public function index()
    {   
        $user = auth()->user();
        $currentCredits = $this->creditService->getCurrentMonthCredits($user);
        $currentCredits['consume_percentage'] = ($currentCredits['available'] / $currentCredits['issued']) * 100 ;
        $activeSubscription =  $this->subs->getActiveSubscriptionDetails($user);
        return view('student.dashboard', [
            'currentCredits' => $currentCredits,
            'activeSubscription' => $activeSubscription,
        ]);
    }
}
