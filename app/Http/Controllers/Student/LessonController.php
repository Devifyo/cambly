<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\LessonService;
use App\Models\Reservation; // <-- Import the Reservation model
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    // Inject the service via the constructor
    public function __construct(private LessonService $lessonService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        
        $validatedFilters = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'filter' => 'nullable|in:upcoming,cancelled,completed',
            'teacher' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        $lessons = $this->lessonService->getPaginatedLessons($user, $validatedFilters);
        $stats = $this->lessonService->getLessonStats($user);

        return view('student.inner.lessons.list', [
            'lessons' => $lessons,
            'filters' => $validatedFilters,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the details for a specific lesson.
     */
    public function lessonDetails(Request $request, $id): View
    {
        $user = $request->user();
        // 1. Find the reservation with its relations
        $reservation = Reservation::query()
            ->with([
                'teacher:id,name',
                'teacher.teacherProfile:user_id,preferred_name,discord_id,tz',
                'availability:id,start_utc,end_utc'
            ])
            ->find(decryptId($id));
            // 2. Authorize: Ensure the user owns this lesson or abort
            if (!$reservation || (int)$reservation->student_id !== (int)$user->id) {
                abort(404, 'Lesson not found.');
            }

        // 3. Get the user's timezone from the service
        $viewerTimezone = $this->lessonService->getViewerTimezone($user);

        // 4. Transform the lesson data using the service
        $lesson = $this->lessonService->transformLesson($reservation, $viewerTimezone);
        // 5. Return the new view
        return view('student.inner.lessons.details', [
            'lesson' => $lesson,
            'userTimezone' => $viewerTimezone // Pass the TZ for display
        ]);
    }
}