<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

// GANTI 'Order' & 'Content' sesuai model di project-mu
use App\Models\Order;
use App\Models\Content;

class Audience extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    // Filter
    public string $range = '30d'; // 7d | 30d | 90d | all
    public ?int $contentId = null;
    public string $search = '';

    // Agar page reset saat filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRange()
    {
        $this->resetPage();
    }

    public function updatingContentId()
    {
        $this->resetPage();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    /**
     * Query dasar transaksi kreator
     */
    protected function baseQuery()
    {
        $creatorId = Auth::id();

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Order::with(['buyer', 'content']) // pastikan relasi buyer() & content() ada di model Order
            ->where('creator_id', $creatorId)
            ->where('status', 'paid');

        // Filter range tanggal
        if ($this->range !== 'all') {
            $days = match ($this->range) {
                '7d'  => 7,
                '90d' => 90,
                default => 30,
            };

            $query->where('paid_at', '>=', now()->subDays($days));
        }

        // Filter konten
        if ($this->contentId) {
            $query->where('content_id', $this->contentId);
        }

        // Search nama / email pembeli
        if (trim($this->search) !== '') {
            $search = '%'.trim($this->search).'%';
            $query->whereHas('buyer', function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        return $query;
    }

    public function render()
    {
        $creatorId = Auth::id();

        $base = $this->baseQuery();

        // METRIK RINGKAS
        $totalTransactions = (clone $base)->count();
        $totalGross        = (clone $base)->sum('amount');
        $totalBuyers       = (clone $base)->distinct('user_id')->count('user_id');
        $avgOrderValue     = $totalTransactions > 0
            ? round($totalGross / $totalTransactions)
            : 0;

        // Pembeli baru 30 hari terakhir (distinct user yang pertama kali beli dalam 30 hari)
        $newBuyerQuery = Order::where('creator_id', $creatorId)
            ->where('status', 'paid')
            ->selectRaw('MIN(paid_at) as first_paid_at, user_id')
            ->groupBy('user_id')
            ->having('first_paid_at', '>=', now()->subDays(30));

        $newBuyers30d = $newBuyerQuery->count('user_id');

        // Transaksi list (per pembelian)
        $transactions = $base->orderByDesc('paid_at')->paginate(10);

        // List konten kreator untuk filter dropdown
        $creatorContents = Content::where('user_id', $creatorId)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('livewire.creator.audience', [
            'transactions'      => $transactions,
            'creatorContents'   => $creatorContents,
            'totalTransactions' => $totalTransactions,
            'totalGross'        => $totalGross,
            'totalBuyers'       => $totalBuyers,
            'newBuyers30d'      => $newBuyers30d,
            'avgOrderValue'     => $avgOrderValue,
        ]);
    }
}
