<?php

namespace App\Livewire\Traits;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

trait ExportLessonsToCsv
{
    // Properties required in the main component:
    // public $exportPeriod = 'last_month';
    // public $exportTeacherId = 'all';
    // public $exportCustomStart = '';
    // public $exportCustomEnd = '';

    public function exportCompletedLessons()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now();
        $rangeLabel = $this->exportPeriod;

        // 1. Determine Date Range Logic
        if ($this->exportPeriod === 'custom') {
            // Validate custom dates
            $this->validate([
                'exportCustomStart' => 'required|date',
                'exportCustomEnd' => 'required|date|after_or_equal:exportCustomStart',
            ]);
            
            $startDate = Carbon::parse($this->exportCustomStart)->startOfDay();
            $endDate = Carbon::parse($this->exportCustomEnd)->endOfDay();
            $rangeLabel = $startDate->format('Ymd') . '-to-' . $endDate->format('Ymd');
        } else {
            // Handle Predefined Ranges
            $startDate = match ($this->exportPeriod) {
                'last_6_months' => $endDate->copy()->subMonths(6)->startOfMonth(),
                default => $endDate->copy()->subMonth()->startOfMonth(), // 'last_month'
            };
        }

        // 2. Query Completed Reservations
        $query = Reservation::query()
            ->completed() // Scope: status = 'completed'
            ->with(['teacher', 'student', 'availability'])
            ->whereHas('availability', function ($q) use ($startDate, $endDate) {
                // Filter by Lesson Start Time within the range
                $q->whereBetween('start_utc', [$startDate, $endDate]);
            });

        // 3. Filter by Specific Teacher
        if ($this->exportTeacherId !== 'all') {
            $query->where('teacher_id', $this->exportTeacherId);
            $teacher = User::find($this->exportTeacherId);
            $teacherName = $teacher ? str_replace(' ', '_', strtolower($teacher->name)) : 'unknown';
            $fileName = "lessons_{$teacherName}_{$rangeLabel}.csv";
        } else {
            $fileName = "all_teachers_lessons_{$rangeLabel}.csv";
        }

        $reservations = $query->get();

        // 4. Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($reservations) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Lesson ID', 
                'Completion Date', 
                'Teacher Name', 
                'Student Name', 
                'Teacher Email',
                'Status'
            ]);

            // CSV Data Rows
            foreach ($reservations as $res) {
                fputcsv($file, [
                    $res->id,
                    // Format Date: YYYY-MM-DD HH:MM
                    $res->availability ? $res->availability->start_utc->format('Y-m-d H:i') : 'N/A',
                    $res->teacher->name ?? 'N/A',
                    $res->student->name ?? 'N/A',
                    $res->teacher->email ?? 'N/A',
                    ucfirst($res->status),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}