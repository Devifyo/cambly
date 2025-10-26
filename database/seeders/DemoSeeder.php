<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TeacherProfile;
use App\Models\StudentProfile;
use App\Models\Availability;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Plans
        Plan::create(['name' => 'Basic', 'credits_per_cycle' => 4, 'price' => 4500]);
        Plan::create(['name' => 'Standard', 'credits_per_cycle' => 8, 'price' => 8000]);
        Plan::create(['name' => 'Premium', 'credits_per_cycle' => 12, 'price' => 9000]);

        // Profiles
        $teachers = User::role('teacher')->get();
        foreach ($teachers as $t) {
            TeacherProfile::create([
                'user_id' => $t->id,
                'preferred_name' => $t->name,
                'age' => rand(25, 40),
                'tz' => 'Asia/Tokyo',
                'discord_id' => "teacher#{$t->id}",
                'bio' => "Experienced teacher #{$t->id} specializing in online game English.",
                'joined_at' => now(),
            ]);
        }

        $students = User::role('student')->get();
        foreach ($students as $s) {
            StudentProfile::create([
                'user_id' => $s->id,
                'preferred_name' => $s->name,
                'age' => rand(18, 35),
                'tz' => 'Asia/Tokyo',
                'native_language' => 'Japanese',
                'english_level' => 'Beginner',
                'discord_id' => "student#{$s->id}",
                'joined_at' => now(),
            ]);
        }

        // Availabilities
        foreach ($teachers as $teacher) {
            for ($i = 0; $i < 5; $i++) {
                $start = Carbon::now()->addDays($i)->setTime(10, 0);
                Availability::create([
                    'teacher_id' => $teacher->id,
                    'start_utc' => $start->copy()->utc(),
                    'end_utc' => $start->copy()->addMinutes(25)->utc(),
                    'is_booked' => false,
                ]);
            }
        }
    }
}
