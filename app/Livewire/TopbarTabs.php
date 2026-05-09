<?php

namespace App\Livewire;

use Livewire\Component;

class TopbarTabs extends Component
{
    public $activeTab = 'DSI';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function render()
    {
        return view('livewire.topbar-tabs');
    }
}
