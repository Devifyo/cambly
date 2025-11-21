<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImpersonationBanner extends Component
{
    public function render()
    {
        // Only render if impersonating
        if (!is_impersonating()) {
            return <<<'blade'
                <div></div>
            blade;
        }

        return view('components.impersonation-banner');
    }
}