<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;

class TicketsIndex extends Component
{
    use WithPagination;

    public bool $sidebarOpen = true;

    public string $search = '';
    public string $statusFilter = 'open';   // open, in_progress, resolved, closed, all
    public string $categoryFilter = 'all';  // all, payout, order, technical, account, other
    public string $sort = 'latest';         // latest, oldest, priority_high, priority_low

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'refreshTickets' => '$refresh',
    ];

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function setStatus(int $id, string $status): void
    {
        if (! in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            return;
        }

        $ticket = SupportTicket::findOrFail($id);

        $ticket->status     = $status;
        $ticket->updated_at = now();
        // ❌ tidak pakai last_reply_at supaya tidak error kolom
        $ticket->save();

        session()->flash('status_admin_tickets', 'Status tiket berhasil diperbarui.');
        $this->dispatch('refreshTickets');
    }

    public function delete(int $id): void
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        session()->flash('status_admin_tickets', 'Tiket support berhasil dihapus.');
        $this->dispatch('refreshTickets');
    }

    public function render()
    {
        $query = SupportTicket::with('user')
            ->when($this->search, function ($q) {
                $s = $this->search;

                $q->where('subject', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%")
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->where('category', $this->categoryFilter);
            });

        // Sorting
        $query->when(true, function ($q) {
            switch ($this->sort) {
                case 'oldest':
                    $q->orderBy('created_at', 'asc');
                    break;
                case 'priority_high':
                    $q->orderByRaw("FIELD(priority, 'high', 'normal', 'low')")
                      ->latest('created_at');
                    break;
                case 'priority_low':
                    $q->orderByRaw("FIELD(priority, 'low', 'normal', 'high')")
                      ->latest('created_at');
                    break;
                default: // latest
                    $q->latest();
            }
        });

        $tickets = $query->paginate(15);

        $stats = [
            'total_all'      => SupportTicket::count(),
            'total_open'     => SupportTicket::where('status', 'open')->count(),
            'total_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'total_resolved' => SupportTicket::where('status', 'resolved')->count(),
            'total_closed'   => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('livewire.admin.tickets-index', [
            'tickets' => $tickets,
            'stats'   => $stats,
        ]);
    }
}
