<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define All Permissions (Grouped for clarity)
        // Note: 'manage_settings' is usually restricted to Super Admin
        $allPermissions = collect([
            // Dashboard
            'view_dashboard',
            
            // Admin Management (Super Admins & Subadmins usually fall here)
            'view_admins', 'create_admins', 'edit_admins', 'delete_admins',
            
            // Ops Management
            'view_ops', 'create_ops', 'edit_ops', 'delete_ops',
            
            // Teacher Management
            'view_teachers', 'create_teachers', 'edit_teachers', 'delete_teachers', 'export_teachers',
            
            // Student Management
            'view_students', 'create_students', 'edit_students', 'delete_students', 
            'book_student_lesson', 'add_student_tickets',
            
            // Subscription Management
            'view_subscriptions', 'create_subscriptions', 'edit_subscriptions', 'delete_subscriptions',
            
            // Global Settings
            'manage_permissions',
        ]);

        // 3. Create Permissions in Database
        $allPermissions->each(function ($permission) {
            Permission::firstOrCreate(['name' => $permission]);
        });

        // -----------------------------------------------------------------
        // 4. ASSIGN ROLES
        // -----------------------------------------------------------------

        // --- A. SUPER ADMIN (Everything) ---
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(
            array_merge($allPermissions, ['manage_settings'])
        );

        // --- B. SUBADMIN (All permissions EXCEPT managing Admins) ---
        // Logic: Filter out any permission containing '_admins'
        // Note: We typically also restrict 'manage_settings' for subadmins, 
        // but per your request, we only strictly exclude '_admins'.
        $subadminPermissions = $allPermissions->filter(function ($permission) {
            return !str_contains($permission, '_admins') 
                && $permission !== 'manage_permissions'; // Typically safer to exclude settings too
        });

        $subadminRole = Role::firstOrCreate(['name' => 'subadmin']);
        $subadminRole->syncPermissions($subadminPermissions);


        // --- C. OPS (All permissions EXCEPT managing Admins AND Ops) ---
        // Logic: Filter out '_admins' AND '_ops'
        $opsPermissions = $allPermissions->filter(function ($permission) {
            return !str_contains($permission, '_admins') 
                && !str_contains($permission, '_ops')
                && !str_contains($permission, '_subscriptions')
                && $permission !== 'manage_permissions';
        });

        $opsRole = Role::firstOrCreate(['name' => 'ops']);
        $opsRole->syncPermissions($opsPermissions);
    }
}