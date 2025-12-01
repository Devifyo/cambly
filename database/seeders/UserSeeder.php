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

        // Teachers
        for ($i = 0; $i < 1; $i++) {
            $t = User::create([
                'name' => "Teacher $i",
                'email' => "teacher$i@mahobook.com",
                'password' => Hash::make('Note@123'),
            ]);
            $t->assignRole('teacher');
        }

        // Students
        for ($i = 0; $i < 1; $i++) {
            $s = User::create([
                'name' => "Student $i",
                'email' => "student$i@mahobook.com",
                'password' => Hash::make('Note@123'),
            ]);
            $s->assignRole('student');
        }
    }
}
