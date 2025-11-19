<?php

namespace App\Livewire\Traits;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

trait ExportLessonsToCsv
{
    // These properties must be declared in the main Livewire component
    // public $exportPeriod = 'last_month';
    // public $exportTeacherId = 'all';

    public function exportCompletedLessons()
    {
        // 1. Determine the date range
        $endDate = Carbon::now();
        $startDate = match ($this->exportPeriod) {
            'last_6_months' => $endDate->copy()->subMonths(6)->startOfMonth(),
            default => $endDate->copy()->subMonth()->startOfMonth(), // 'last_month'
        };

        // 2. Query Completed Reservations
        $query = Reservation::query()
            ->completed()
            ->with(['teacher', 'student', 'availability'])
            ->whereHas('availability', function ($q) use ($startDate, $endDate) {
                // Ensure the completed lesson fell within the requested time frame
                $q->whereBetween('start_utc', [$startDate, $endDate]);
            });

        // 3. Filter by Specific Teacher
        if ($this->exportTeacherId !== 'all') {
            $query->where('teacher_id', $this->exportTeacherId);
            $teacher = User::find($this->exportTeacherId);
            $fileName = 'lessons_completed_by_' . ($teacher->name ?? 'unknown') . '_' . $this->exportPeriod . '.csv';
        } else {
            $fileName = 'all_lessons_completed_' . $this->exportPeriod . '.csv';
        }

        $reservations = $query->get();

        // 4. Generate CSV Headers and Data
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($reservations) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, [
                'Reservation ID', 
                'Teacher Name', 
                'Student Name', 
                'Lesson Time (UTC)', 
                'Lesson Status', 
                'Teacher Email'
            ]);

            // Data Rows
            foreach ($reservations as $res) {
                fputcsv($file, [
                    $res->id,
                    $res->teacher->name ?? 'N/A',
                    $res->student->name ?? 'N/A',
                    $res->availability->start_utc->format('Y-m-d H:i') ?? 'N/A',
                    ucfirst($res->status),
                    $res->teacher->email ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        // 5. Stream the download response
        return Response::stream($callback, 200, $headers);
    }
}