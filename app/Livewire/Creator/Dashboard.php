<?php

namespace App\Livewire\Creator;

use Livewire\Component;

class Dashboard extends Component
{
    public bool $sidebarOpen = true; // <— penting

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function render()
    {
        // nanti bisa isi data beneran di sini
        return view('livewire.creator.dashboard', [
            'totalContents'    => 0,
            'totalBuyers'      => 0,
            'estimatedIncome'  => 0,
        ]);
    }
}
