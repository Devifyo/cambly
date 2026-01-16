<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@mahobook.com'],
            ['name' => 'Admin User', 'password' => Hash::make('pass@admin'), 'status' => true]
        );
        $admin->assignRole('admin');

        // Ops
        $ops = User::firstOrCreate(
            ['email' => 'ops@mahobook.com'],
            ['name' => 'Ops Manager', 'password' => Hash::make('pass@ops'), 'status' => true]
        );
        $ops->assignRole('ops');

        // Teachers (Fixed using firstOrCreate)
        for ($i = 0; $i < 1; $i++) {
            $t = User::firstOrCreate(
                ['email' => "teacher$i@mahobook.com"], // Check for this email first
                [
                    'name' => "Teacher $i",
                    'password' => Hash::make('Note@123'),
                ]
            );
            $t->assignRole('teacher');
        }

        // Students (Fixed using firstOrCreate)
        for ($i = 0; $i < 1; $i++) {
            $s = User::firstOrCreate(
                ['email' => "student$i@mahobook.com"], // Check for this email first
                [
                    'name' => "Student $i",
                    'password' => Hash::make('Note@123'),
                ]
            );
            $s->assignRole('student');
        }
    }
}