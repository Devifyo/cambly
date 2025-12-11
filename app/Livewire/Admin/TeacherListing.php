<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\TeacherProfile;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\ExportLessonsToCsv; 

class TeacherListing extends Component
{
    use WithPagination;
    use ExportLessonsToCsv; 

    // --- Listing & Sorting Properties ---
    public $search = '';
    public $statusFilter = ''; // Will hold 1 or 0
    public $genderFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // --- Export Properties (Used by Trait) ---
    public $exportPeriod = 'last_month';
    public $exportTeacherId = 'all';

    // --- CRUD Properties ---
    public $userId;
    public $name, $email, $password, $password_confirmation;
    public $status; // Will hold 1 or 0
    public $editMode = false;
    
    // --- Teacher Profile Properties ---
    public $gender;
    public $native_language;

    protected $listeners = ['deleteConfirmed' => 'destroy'];

    protected function rules()
    {                  
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->editMode ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'status' => 'required|integer|in:0,1',
            'gender' => 'required|string|in:male,female,other',
            'native_language' => 'nullable|string|max:255',
        ];
    }

    public function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation', 'status', 'editMode', 'gender', 'native_language']);
        $this->resetValidation();
    }
    
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingGenderFilter() { $this->resetPage(); }
    
    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'genderFilter']);
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }
    
    public function openExportModalForTeacher($teacherId)
    {
        $this->exportPeriod = 'last_month';
        $this->exportTeacherId = $teacherId;
        $this->dispatch('showExportModal');
    }

    // --- CRUD Methods ---

    public function create()
    {
        $this->resetForm();
        $this->status = 1; // Default status: Active
        $this->editMode = false;
        $this->dispatch('showAddModal');
    }

    public function store()
    {   
       
        $this->validate();

        try {
            DB::transaction(function () {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'status' => $this->status,
                    'gender' => $this->gender, 
                ]);

                $user->assignRole(config('roles.teacher'));

                TeacherProfile::create([
                    'preferred_name' => $this->name,
                    'tz' => getTimeZone(),
                    'user_id' => $user->id,
                    'gender' => $this->gender,
                    'native_language' => $this->native_language,
                ]);
            });

            $this->dispatch('hideAddModal');
            $this->dispatch('alert', type: 'success', message: 'Teacher created successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Failed to create teacher.');
        }
    }

    public function edit($id)
    {
        $this->resetForm();
        $user = User::teachers()->with('teacherProfile')->findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = (int) $user->status; // Cast status to integer
        $this->editMode = true;
        
        $this->gender = $user->teacherProfile->gender ?? null;
        $this->native_language = $user->teacherProfile->native_language ?? null;

        $this->dispatch('showEditModal');
    }

    public function update()
    {   
       
        $this->validate($this->rules());

        try {
            DB::transaction(function () {
                $user = User::teachers()->findOrFail($this->userId);

                $data = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'status' => $this->status,
                    'gender' => $this->gender,
                ];

                if (!empty($this->password)) {
                    $data['password'] = Hash::make($this->password);
                }

                $user->update($data);
                
                $user->teacherProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [   
                        'tz' => $user->teacherProfile->tz ?? getTimeZone(),
                        'preferred_name' => $this->name,
                        'gender' => $this->gender,
                        'native_language' => $this->native_language,
                    ]
                );
            });
            
            $this->dispatch('hideEditModal');
            $this->dispatch('alert', type: 'success', message: 'Teacher updated successfully.');
            $this->resetForm();

        } catch (\Exceptions $e) {
            $this->dispatch('alert', type: 'error', message: 'Failed to update teacher.');
        }
    }

    public function deleteConfirmation($id)
    {
        $this->userId = $id;
        $this->dispatch('showDeleteModal');
    }

    public function destroy()
    {
        try {
            User::teachers()->findOrFail($this->userId)->delete();
            $this->dispatch('hideDeleteModal');
            $this->dispatch('alert', type: 'success', message: 'Teacher deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Failed to delete teacher.');
        }
        $this->resetForm();
    }
    
    public function render()
    {
        $teachers = User::teachers()
            ->withCount(['reservationsAsTeacher' => function ($query) {
                $query->completed();
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            // Filter by 0 or 1
            ->when(in_array($this->statusFilter, ['0', '1']), function ($query) {
                $query->where('status', (int) $this->statusFilter);
            })
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            // Apply sorting logic
            ->when($this->sortField === 'completed_lessons_count', function($query) {
                $query->orderBy('reservations_as_teacher_count', $this->sortDirection);
            })
            ->when($this->sortField !== 'completed_lessons_count', function($query) {
                 $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(10)
            ->withPath(url()->current());
            
        $allTeachers = User::teachers()->select('id', 'name')->get();

        return view('livewire.admin.teacher-listing', [
            'teachers' => $teachers,
            'allTeachers' => $allTeachers,
        ]);
    }
}