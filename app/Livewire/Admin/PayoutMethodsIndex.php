<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PayoutMethod;

class PayoutMethodsIndex extends Component
{
    use WithPagination;

    public bool $sidebarOpen = true;

    public string $search = '';
    public string $statusFilter = 'pending'; // default fokus ke pending
    public string $typeFilter = 'all';

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => 'pending'],
        'typeFilter'   => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function approve(int $id): void
    {
        $method = PayoutMethod::findOrFail($id);

        $method->update([
            'status'      => 'verified',
            'verified_at' => now(),
            'notes'       => null,
        ]);

        session()->flash('status_admin_payout', 'Metode penarikan berhasil diverifikasi.');
    }

    public function reject(int $id, string $reason = null): void
    {
        $method = PayoutMethod::findOrFail($id);

        $method->update([
            'status'      => 'rejected',
            'verified_at' => null,
            'notes'       => $reason,
        ]);

        session()->flash('status_admin_payout', 'Metode penarikan ditolak & dikembalikan ke kreator.');
    }

    public function render()
    {
        $query = PayoutMethod::with('user')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', '%'.$this->search.'%')
                       ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                ->orWhere('provider', 'like', '%'.$this->search.'%')
                ->orWhere('account_number', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== 'all', function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->latest();

        return view('livewire.admin.payout-methods-index', [
            'methods'         => $query->paginate(15),
            'totalPending'    => PayoutMethod::where('status', 'pending')->count(),
            'totalVerified'   => PayoutMethod::where('status', 'verified')->count(),
            'totalRejected'   => PayoutMethod::where('status', 'rejected')->count(),
        ]);
    }
}
