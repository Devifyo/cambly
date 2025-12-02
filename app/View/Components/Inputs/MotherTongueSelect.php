<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class MotherTongueSelect extends Component
{
    public $selected;

    public function __construct()
    {
        $user = Auth::user();
        
        // 1. Try getting from old input (validation error)
        // 2. Try getting from Database
        // 3. Default to null
        $dbValue = $user ? $user->languages()->wherePivot('type', 'mother_tongue')->value('code') : null;
        
        $this->selected = old('mother_tongue', $dbValue);
    }

    public function render()
    {
        return view('components.inputs.mother-tongue-select');
    }
}