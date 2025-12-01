<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManagePermission extends Component
{
    public $roles;
    
    // Define the structure here for the View to use
    public $groupedPermissions = [
        'Dashboard'     => ['view_dashboard'],
        'Admins'        => ['view_admins', 'create_admins', 'edit_admins', 'delete_admins'],
        'Ops Managers'  => ['view_ops', 'create_ops', 'edit_ops', 'delete_ops'],
        'Teachers'      => ['view_teachers', 'create_teachers', 'edit_teachers', 'delete_teachers', 'export_teachers'],
        'Students'      => ['view_students', 'create_students', 'edit_students', 'delete_students', 'book_student_lesson', 'add_student_tickets'],
        'Subscriptions' => ['view_subscriptions', 'create_subscriptions', 'edit_subscriptions', 'delete_subscriptions'],
        'Settings'      => ['manage_permissions'],
    ];

    public function mount()
    {
        // Get all roles 
        $this->roles = Role::whereNotIn('name', [config('roles.admin'), config('roles.student'), config('roles.teacher')])->get();
    }

    public function togglePermission($roleId, $permissionName)
    {
        $role = Role::findById($roleId);
        
        // Prevent locking out Super Admin from settings
        if($role->name === config('roles.admin') && $permissionName === 'manage_permissions') {
            $this->dispatch('alert', type: 'error', message: 'Cannot revoke Settings from Admin.');
            return;
        }

        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
        } else {
            $role->givePermissionTo($permissionName);
        }

        $this->dispatch('alert', type: 'success', message: 'Permission updated.');
    }

    public function render()
    {
        return view('livewire.admin.manage-permission');
    }
}