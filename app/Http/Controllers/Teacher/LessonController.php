<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Teacher\TeacherLessonService;
use App\Models\Reservation; // <-- Import the Reservation model
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use App\Traits\BookingLessonEmailTrait;
class LessonController extends Controller
{   
    use BookingLessonEmailTrait;

    public $view_path = 'teacher.lessons';
    // Inject the service via the constructor
    public function __construct(private TeacherLessonService $lessonService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
            
        $validatedFilters = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'filter' => 'nullable|in:upcoming,cancelled,completed',
            'student' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);
        
        $lessons = $this->lessonService->getPaginatedLessons($user, $validatedFilters);
        $stats = $this->lessonService->getLessonStats($user);

        return view($this->view_path.'.list', [
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
                'teacher.teacherProfile:user_id,preferred_name',
                'availability:id,start_utc,end_utc'
            ])
            ->find(decryptId($id));
            // 2. Authorize: Ensure the user owns this lesson or abort
            if (!$reservation || (int)$reservation->teacher_id !== (int)$user->id) {
                abort(404, 'Lesson not found.');
            }

        // 3. Get the user's timezone from the service
        $viewerTimezone = $this->lessonService->getViewerTimezone($user);

        // 4. Transform the lesson data using the service
        $lesson = $this->lessonService->transformLesson($reservation, $viewerTimezone);
        // 5. Return the new view
        return view($this->view_path.'.details', [
            'lesson' => $lesson,
            'userTimezone' => $viewerTimezone // Pass the TZ for display
        ]);
    }

    public function updateLessonLink(Request $request, $id)
    {
        // 1. Validate the input
        $validator = Validator::make($request->all(), [
            // Allow an empty link (for removal) but if present, must be a valid URL
            'lesson_meeting_link' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        
        // 2. Find the reservation
        try {
            // Find the lesson, ensuring it belongs to the authenticated teacher
            $reservation = Reservation::where('id', decryptId($id))
                ->where('teacher_id', auth()->id()) // <-- IMPORTANT security check
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Lesson not found.'], 404);
        }

        // 3. Check lesson status
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
             return response()->json(['success' => false, 'message' => 'Cannot update a completed or cancelled lesson.'], 403);
        }

        // 4. Update the link (will be set to null if request input is empty)
        $reservation->lesson_meeting_link = $request->input('lesson_meeting_link');
        $reservation->save();
        $this->sendLessonLinkUpdatedEmail($reservation);

        // 5. Return a success response
        return response()->json([
            'success'  => true,
            'new_link' => $reservation->lesson_meeting_link,
            'message'  => 'Meeting link updated successfully.'
        ]);
    }
}
