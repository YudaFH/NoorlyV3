<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Withdraw;

class WithdrawsIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    public string $search       = '';
    public string $statusFilter = 'all';   // all | pending | approved | paid | rejected
    public string $dateFilter   = '30d';   // all | today | 7d | 30d | 90d
    public string $sort         = 'latest';// latest | oldest | amount_high | amount_low

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFilter'   => ['except' => '30d'],
        'sort'         => ['except' => 'latest'],
    ];

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    protected function baseQuery()
    {
        return Withdraw::query()
            ->with(['user']); // pastikan di model Withdraw ada relasi user()
    }

    public function updateStatus(int $withdrawId, string $newStatus): void
    {
        if (! in_array($newStatus, ['pending', 'approved', 'paid', 'rejected'], true)) {
            return;
        }

        $withdraw = Withdraw::find($withdrawId);

        if (! $withdraw) {
            return;
        }

        $withdraw->status = $newStatus;

        // Kalau ditandai paid dan belum ada paid_at (kalau kolomnya ada)
        if ($newStatus === 'paid' && empty($withdraw->paid_at)) {
            $withdraw->paid_at = now();
        }

        $withdraw->save();

        session()->flash(
            'status_admin_withdraws',
            "Status penarikan #{$withdraw->id} berhasil diubah menjadi {$newStatus}."
        );
    }

    public function render()
    {
        $query = $this->baseQuery();

        // Search: ID, nama kreator, email
        if ($this->search !== '') {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%'.$search.'%')
                          ->orWhere('email', 'like', '%'.$search.'%');
                  });
            });
        }

        // Filter status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filter tanggal
        if ($this->dateFilter !== 'all') {
            if ($this->dateFilter === 'today') {
                $query->whereDate('created_at', now()->toDateString());
            } else {
                $days = match ($this->dateFilter) {
                    '7d'  => 7,
                    '30d' => 30,
                    '90d' => 90,
                    default => null,
                };

                if ($days) {
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                $query->orderBy('amount', 'desc'); // pastikan kolom `amount` ada
                break;
            case 'amount_low':
                $query->orderBy('amount', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $withdraws = $query->paginate(15);

        // Statistik (global, tanpa filter)
        $totalAll      = Withdraw::count();
        $totalPending  = Withdraw::where('status', 'pending')->count();
        $totalApproved = Withdraw::where('status', 'approved')->count();
        $totalPaid     = Withdraw::where('status', 'paid')->count();
        $totalRejected = Withdraw::where('status', 'rejected')->count();

        $totalRequested = Withdraw::sum('amount');
        $totalPaidAmount = Withdraw::where('status', 'paid')->sum('amount');

        return view('livewire.admin.withdraws-index', [
            'withdraws'       => $withdraws,
            'totalAll'        => $totalAll,
            'totalPending'    => $totalPending,
            'totalApproved'   => $totalApproved,
            'totalPaid'       => $totalPaid,
            'totalRejected'   => $totalRejected,
            'totalRequested'  => $totalRequested,
            'totalPaidAmount' => $totalPaidAmount,
        ]);
    }
}
