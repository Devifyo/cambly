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
            ['email' => 'admin@mailinator.com'],
            ['name' => 'Admin User', 'password' => Hash::make('Note@123'), 'status' => true]
        );
        $admin->assignRole('admin');

        // Ops
        $ops = User::firstOrCreate(
            ['email' => 'ops@mailinator.com'],
            ['name' => 'Ops Manager', 'password' => Hash::make('Note@123'), 'status' => true]
        );
        $ops->assignRole('ops');

        // Teachers
        for ($i = 1; $i <= 5; $i++) {
            $t = User::create([
                'name' => "Teacher $i",
                'email' => "teacher$i@mailinator.com",
                'password' => Hash::make('Note@123'),
            ]);
            $t->assignRole('teacher');
        }

        // Students
        for ($i = 1; $i <= 10; $i++) {
            $s = User::create([
                'name' => "Student $i",
                'email' => "student$i@mailinator.com",
                'password' => Hash::make('Note@123'),
            ]);
            $s->assignRole('student');
        }
    }
}
