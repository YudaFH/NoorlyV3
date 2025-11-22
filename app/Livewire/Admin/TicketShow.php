<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SupportTicket;

class TicketShow extends Component
{
    public bool $sidebarOpen = true;

    public SupportTicket $ticket;
    public string $status;

    public function mount(SupportTicket $ticket): void
    {
        $this->ticket = $ticket;
        $this->status = $ticket->status ?? 'open';
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updateStatus(string $status): void
    {
        if (! in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            return;
        }

        $this->ticket->status     = $status;
        $this->ticket->updated_at = now();
        $this->ticket->save();

        $this->status = $status;

        session()->flash('status_admin_tickets', 'Status tiket berhasil diperbarui.');
    }

    public function deleteTicket()
    {
        $this->ticket->delete();

        session()->flash('status_admin_tickets', 'Tiket support berhasil dihapus.');

        return redirect()->route('admin.tickets.index');
    }


    public function render()
    {
        return view('livewire.admin.ticket-show');
    }
}
