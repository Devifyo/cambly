<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NativeLanguageSelect extends Component
{
    public $selected;

    public function __construct()
    {
        $user = Auth::user();

        // 1. Try getting from old input
        // 2. Try getting from Database (pluck codes to array)
        // 3. Default to empty array
        $dbValues = $user ? $user->languages()->wherePivot('type', 'native')->pluck('code')->toArray() : [];
        
        $this->selected = old('native_languages', $dbValues);
    }

    public function render()
    {
        return view('components.inputs.native-language-select');
    }
}