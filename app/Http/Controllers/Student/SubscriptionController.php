<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class SubscriptionController extends Controller
{   
    protected $view_path = 'student.inner.subscription';

    public function index()
    {
        $monthlyPlans = Plan::active()->where('interval', 'monthly')->get();
        $trialPlan = Plan::active()->where('interval', 'one_time')->first();

        return view($this->view_path . '.pricing', [
            'monthlyPlans' => $monthlyPlans,
            'trialPlan' => $trialPlan,
        ]);
    }
}
