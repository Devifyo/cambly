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

    // --- Search & Filters ---
    public $search = '';
    public $statusFilter = '';
    public $genderFilter = '';
    
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // --- Export Properties ---
    public $exportPeriod = 'last_month';
    public $exportTeacherId = 'all';
    public $exportCustomStart = ''; // For custom range
    public $exportCustomEnd = '';   // For custom range

    // --- Form Properties ---
    public $userId;
    public $name, $email, $password, $password_confirmation;
    public $status = 1; 
    public $editMode = false;

    // --- Profile Data ---
    public $gender;
    public $discord_id;
    public $headline;
    public $teaching_experience;
    public $country_residence;
    public $japanese_level;

    protected $listeners = ['deleteConfirmed' => 'destroy'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->editMode ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'status' => 'required|integer|in:0,1',
            'gender' => 'required|string|in:male,female,other',
            'discord_id' => 'required|string|max:50',
            'country_residence' => 'required|string|max:100',
            'japanese_level' => 'required|string',
            'headline' => 'nullable|string|max:255',
            'teaching_experience' => 'nullable|string|max:1000',
        ];
    }

    public function resetForm()
    {
        $this->reset([
            'userId', 'name', 'email', 'password', 'password_confirmation', 'status', 'editMode',
            'gender', 'discord_id', 'headline', 'teaching_experience',
            'country_residence', 'japanese_level'
        ]);
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
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
    }

    public function openExportModalForTeacher($teacherId)
    {
        $this->exportPeriod = 'last_month';
        $this->exportTeacherId = $teacherId;
        $this->exportCustomStart = '';
        $this->exportCustomEnd = '';
        $this->dispatch('showExportModal');
    }

    // --- CRUD Methods ---

    public function create()
    {
        $this->resetForm();
        $this->status = 1;
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
                    'user_id' => $user->id,
                    'preferred_name' => $this->name,
                    'tz' => 'UTC', 
                    'gender' => $this->gender,
                    'discord_id' => $this->discord_id,
                    'short_bio' => $this->headline,
                    'experience' => $this->teaching_experience,
                    'country_residence' => $this->country_residence,
                    'english_level' => $this->japanese_level,
                ]);
            });

            $this->dispatch('hideAddModal');
            $this->dispatch('alert', type: 'success', message: 'Teacher created successfully.');
            $this->resetForm();
        } catch (\Exception $e) {
            \Log::error($e);
            $this->dispatch('alert', type: 'error', message: 'Failed to create teacher.');
        }
    }

    public function edit($id)
    {
        $this->resetForm();
        $user = User::with('teacherProfile')->findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = (int) $user->status;
        $this->gender = $user->gender;
        $this->editMode = true;

        if ($user->teacherProfile) {
            $this->discord_id = $user->teacherProfile->discord_id;
            $this->headline = $user->teacherProfile->short_bio;
            $this->teaching_experience = $user->teacherProfile->experience;
            $this->country_residence = $user->teacherProfile->country_residence;
            $this->japanese_level = $user->teacherProfile->english_level;
        }
        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $user = User::findOrFail($this->userId);
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'status' => $this->status,
                    'gender' => $this->gender,
                ];
                if ($this->password) {
                    $userData['password'] = Hash::make($this->password);
                }
                $user->update($userData);

                $user->teacherProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'preferred_name' => $this->name,
                        'gender' => $this->gender,
                        'discord_id' => $this->discord_id,
                        'short_bio' => $this->headline,
                        'experience' => $this->teaching_experience,
                        'country_residence' => $this->country_residence,
                        'english_level' => $this->japanese_level,
                    ]
                );
            });

            $this->dispatch('hideEditModal');
            $this->dispatch('alert', type: 'success', message: 'Teacher updated successfully.');
            $this->resetForm();
        } catch (\Exception $e) {
            \Log::error($e);
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
            User::findOrFail($this->userId)->delete();
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
            ->withCount(['reservationsAsTeacher' => fn($q) => $q->completed()])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when(in_array($this->statusFilter, ['0', '1']), fn($q) => $q->where('status', (int) $this->statusFilter))
            ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
            ->orderBy($this->sortField === 'completed_lessons_count' ? 'reservations_as_teacher_count' : $this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.teacher-listing', [
            'teachers' => $teachers,
            'allTeachers' => User::teachers()->select('id', 'name')->get(),
        ]);
    }
}