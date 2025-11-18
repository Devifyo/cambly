<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class AlertHandler extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'info';

    #[On('alert')]
    public function handleAlert($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.admin.components.alert-handler');
    }
}