<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OpsListing extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $statusFilter = '';
    
    // Form Fields
    public $opsId;
    public $name, $email, $status = 1;

    // Password Fields
    public $password;
    public $password_confirmation;

    // Sort
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        // Fetch Users with 'ops' role
        $opsUsers = User::role(config('roles.ops', 'ops')) 
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.ops-listing', [
            'opsUsers' => $opsUsers
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
            'password' => 'required|min:8|confirmed',
            'status' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => $this->status,
        ]);

        // Assign Ops Role
        $user->assignRole(config('roles.ops', 'ops'));

        $this->dispatch('hideAddModal');
        $this->dispatch('alert', type: 'success', message: 'Ops Manager created successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        
        $this->opsId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->status;
        
        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->opsId)],
            'password' => 'nullable|min:8|confirmed',
            'status' => 'required|boolean',
        ]);

        $user = User::findOrFail($this->opsId);

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
        $this->dispatch('alert', type: 'success', message: 'Ops Manager updated successfully!');
        $this->resetForm();
    }

    public function deleteConfirmation($id)
    {
        $this->opsId = $id;
        $this->dispatch('showDeleteModal');
    }

    public function destroy()
    {
        $user = User::findOrFail($this->opsId);
        
        if ($user->id === auth()->id()) {
             $this->dispatch('hideDeleteModal');
             $this->dispatch('alert', type: 'error', message: 'You cannot delete yourself.');
             return;
        }

        $user->delete();

        $this->dispatch('hideDeleteModal');
        $this->dispatch('alert', type: 'success', message: 'Ops Manager deleted successfully!');
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
        $this->opsId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
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