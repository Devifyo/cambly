<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SubadminListing extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $statusFilter = '';
    
    // Form Fields
    public $subadminId;
    public $name, $email, $status = 1;
    
    // Password Fields
    public $password;
    public $password_confirmation; // Added for confirmation

    // Sort
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        // FIXED: Changed User::subadmins() to User::role(...)
        $subadmins = User::role(config('roles.subadmin', 'subadmin')) 
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.subadmin-listing', [
            'subadmins' => $subadmins
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('showAddModal');
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Added 'confirmed' rule
            'status' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => $this->status,
        ]);

        // Assign Role
        $user->assignRole(config('roles.subadmin', 'subadmin'));

        $this->dispatch('hideAddModal');
        $this->dispatch('alert', type: 'success', message: 'Subadmin created successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        
        $this->subadminId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->status;
        
        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->subadminId)],
            'password' => 'nullable|min:8|confirmed', // Added 'confirmed' rule
            'status' => 'required|boolean',
        ]);

        $user = User::findOrFail($this->subadminId);

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
        ];

        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);

        $this->dispatch('hideEditModal');
        $this->dispatch('alert', type: 'success', message: 'Subadmin updated successfully!');
        $this->resetForm();
    }

    public function deleteConfirmation($id)
    {
        $this->subadminId = $id;
        $this->dispatch('showDeleteModal');
    }

    public function destroy()
    {
        $user = User::findOrFail($this->subadminId);
        
        if ($user->id === auth()->id()) {
             $this->dispatch('hideDeleteModal');
             $this->dispatch('alert', type: 'error', message: 'You cannot delete yourself.');
             return;
        }

        $user->delete();

        $this->dispatch('hideDeleteModal');
        $this->dispatch('alert', type: 'success', message: 'Subadmin deleted successfully!');
        $this->resetForm();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetForm()
    {
        $this->subadminId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = ''; // Reset confirmation
        $this->status = 1;
        $this->resetErrorBag();
        $this->resetValidation();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
    }
}