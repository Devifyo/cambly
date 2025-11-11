<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public ?string $type = null;
    public ?string $message = null;

    public function __construct()
    {
        // Detect any flash messages automatically
        foreach (['success', 'error', 'warning', 'info'] as $key) {
            if (session()->has($key)) {
                $this->type = $key;
                $this->message = session($key);
                break;
            }
        }
    }

    public function render()
    {
        return view('components.alert');
    }
}
