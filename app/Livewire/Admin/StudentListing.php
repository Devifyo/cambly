<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\TicketLedger;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentListing extends Component
{
    use WithPagination;

    // --- Public Properties (Livewire can serialize these) ---
    public $search = '';
    public $statusFilter = '';
    public $subscriptionFilter = '';

    public $studentId;
    public $name, $email, $password, $password_confirmation, $status;
    public $editMode = false;
    public $creditStudentId;
    public $creditsToAdjust;
    public $creditsAdjustmentReason;
    

    protected $listeners = ['deleteConfirmed' => 'destroy'];
    
    protected $creditRules = [
        'creditsToAdjust' => 'required|integer|not_in:0',
        'creditsAdjustmentReason' => 'required|string|max:500',
    ];

  

    /**
     * Dynamic validation rules for create/edit.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->studentId),
            ],
            'password' => $this->editMode ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'status' => 'required|in:1,0',
        ];
    }

    public function resetForm()
    {
        $this->reset(['studentId', 'name', 'email', 'password', 'password_confirmation', 'status', 'editMode', 'creditsToAdjust', 'creditsAdjustmentReason']);
        $this->resetValidation();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSubscriptionFilter()
    {
        $this->resetPage();
    }

    
    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter' ,'subscriptionFilter']);
        $this->resetPage();
    }

    // --- ACCURATE CREDIT CALCULATION HELPER (Calls the Service) ---
    
    /**
     * Calculates the available credits using the injected UseCreditService.
     */
    public function getStudentTotalAvailableCredits(User $student): int
    {
       return get_current_month_credits($student,'show_all');
    }
    
    // --- CRUD Methods ---

    public function create()
    {
        $this->resetForm();
        $this->status = 'active';
        $this->editMode = false;
        $this->dispatch('showAddModal');
    }

    public function store()
    {
        $this->validate();

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'status' => $this->status,
            ]);

            $user->assignRole(config('roles.student'));

            $this->dispatch('hideAddModal');
            $this->dispatch('alert', type : 'success', message : 'Student created successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            $this->dispatch('alert', type : 'error', message : 'Failed to create student.');
        }
    }

    public function edit($id)
    {
        $this->resetForm();
        $student = User::students()->findOrFail($id);

        $this->studentId = $student->id;
        $this->name = $student->name;
        $this->email = $student->email;
        $this->status = $student->status;
        $this->editMode = true;

        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $this->validate($this->rules());

        try {
            $student = User::students()->findOrFail($this->studentId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $student->update($data);

            $this->dispatch('hideEditModal');
            $this->dispatch('alert', type : 'success', message : 'Student updated successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            $this->dispatch('alert', type : 'error', message : 'Failed to update student.');
        }
    }

    public function deleteConfirmation($id)
    {
        $this->studentId = $id;
        $this->dispatch('showDeleteModal');
    }

    public function destroy()
    {
        try {
            User::students()->findOrFail($this->studentId)->delete();
            $this->dispatch('hideDeleteModal');
            $this->dispatch('alert', type : 'success', message : 'Student deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type : 'error', message : 'Failed to delete student.');
        }
        $this->resetForm();
    }

    // --- Credits Management Methods ---

    public function adjustCreditsModal($id)
    {
        $this->resetForm();
        $this->creditStudentId = $id;
        $this->creditsToAdjust = 0;
        $this->creditsAdjustmentReason = '';
        $this->dispatch('showCreditsModal');
    }

    public function adjustCredits()
    {
        $this->validate($this->creditRules, [
            'creditsToAdjust.not_in' => 'The credits field must be a non-zero integer (e.g., 5 or -5).',
        ]);
        try {
            $student = User::students()->findOrFail($this->creditStudentId);
            $currenMonthLeadger = getCurrentTicketLedger($student,'show_all');
            if($currenMonthLeadger){
                $cycleNumber = $currenMonthLeadger->cycle_number;
            }else{
                $cycleNumber = 0;
            }

            $amount = (int) $this->creditsToAdjust;

            DB::transaction(function () use ($student, $amount, $cycleNumber) {
                // Record the adjustment using cycle_number 0 for manual adjustments
                TicketLedger::create([
                    'student_id' => $student->id,
                    'issued_credits' => $amount > 0 ? $amount : 0,
                    'used_credits' => $amount < 0 ? abs($amount) : 0,
                    'cycle_number' => $cycleNumber, // Manual/Admin entry
                    'credit_by' => 'admin',
                    'reason' => $this->creditsAdjustmentReason,
                ]);
            });

            $this->dispatch('hideCreditsModal');
            $this->dispatch('alert', type : 'success', message : "Successfully adjusted {$amount} credits for {$student->name}.");
            $this->resetForm();

        } catch (\Exception $e) {
            $this->dispatch('alert', type : 'error', message : 'Failed to adjust credits.');
        }
    }

    public function Impersonate($studentId)
{   
        
    $student = User::findOrFail(decryptId($studentId));
    
    // Use the package's built-in method
    if (Auth::user()->canImpersonate()) {
        Auth::user()->impersonate($student);
        
        // Redirect to the student's booking page
        return redirect()->route('search.tutors');
    }

    session()->flash('error', 'Unauthorized action.');
    return;
}

    // --- Rendering Logic ---
    public function render()
    {
        $students = User::students()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter === 'active' ? '1' : '0');
            })
            ->when($this->subscriptionFilter, function ($query) {
                if ($this->subscriptionFilter === 'yes') {
                    // has active subscription
                    $query->whereHas('activeSubscription');
                } else {
                    // no subscription
                    $query->whereDoesntHave('activeSubscription');
                }
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.student-listing', [
            'students' => $students,
        ]);
    }
}