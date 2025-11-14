<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Teacher\{TeacherDashboardService};
use Carbon\Carbon;
class TeacherDashboardController extends Controller
{   
    public $view_path = 'teacher';
    protected TeacherDashboardService $teacherDashboardService;
    public function __construct( TeacherDashboardService $teacherDashboardService)
    {
         $this->teacherDashboardService = $teacherDashboardService;
    }

    /******* functions *********/

    public function index()
    {   
      $dashbordDetails = $this->teacherDashboardService->getStudentDashboardData(auth()->user());
      return view($this->view_path.'.dashboard',[
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
        $events = $this->teacherDashboardService->getStudentReservationsForMonth($user, $date);
        // Return the data as JSON
        return response()->json($events);
    }
  
}
