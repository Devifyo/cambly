<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Reservation;
use App\Livewire\Traits\DashboardStats; // ⬅️ Use the Trait
use Livewire\Component;

class Dashboard extends Component
{
    use DashboardStats;

    public $recentTeachers;
    public $recentStudents;

    public function mount()
    {
        $this->computeStats();

        // Fetch recent teachers (last 3, along with their profile)
        $this->recentTeachers = User::teachers()
            ->with('teacherProfile')
            ->latest()
            ->limit(3)
            ->get();
            
        // Fetch recent students (last 3, along with their profile)
        $this->recentStudents = User::students()
            ->with('studentProfile')
            ->latest()
            ->limit(3)
            ->get();
    }
    
    // We don't need a separate render() method here if we're just injecting data
    // but if you were to refresh or update parts, you might use it.
    // For this dashboard, mount() is sufficient.

    public function render()
    {
        // Since the data is ready in the public properties, we just load the view
        return view('livewire.admin.dashboard');
    }
}