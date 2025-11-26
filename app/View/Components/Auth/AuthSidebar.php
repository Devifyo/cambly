<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthSidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $heading;
    public $description;
    public function __construct($heading = null, $description = null)
    {
       $this->heading = $heading;
       $this->description = $description;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.auth-sidebar');
    }
}
