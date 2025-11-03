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
        if (
            isset($currentCredits['available'], $currentCredits['issued']) &&
            $currentCredits['issued'] > 0
        ) {
            $percentage = ($currentCredits['available'] / $currentCredits['issued']) * 100;
            $currentCredits['consume_percentage'] = number_format($percentage, 2);
        } else {
            $currentCredits['consume_percentage'] = '0';
        }
        $activeSubscription =  $this->subs->getActiveSubscriptionDetails($user);
        return view('student.dashboard', [
            'currentCredits' => $currentCredits,
            'activeSubscription' => $activeSubscription,
        ]);
    }
}
