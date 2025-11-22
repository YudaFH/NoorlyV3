<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Content;
use App\Models\PayoutMethod;

class CreatorsIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    // Filter & search
    public ?string $search        = null;
    public string $trustedFilter  = 'all';      // all | trusted | non_trusted
    public string $payoutFilter   = 'all';      // all | verified | unverified
    public string $sort           = 'newest';   // newest | oldest | most_revenue | most_buyers | most_contents
    public int $perPage           = 10;

    // Detail modal
    public ?int $detailUserId     = null;
    public bool $showDetailModal  = false;

    // Simpel toggle sidebar
    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTrustedFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPayoutFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    protected function baseQuery()
    {
        // Hanya user dengan role creator
        $query = User::query()
            ->where('role', 'creator');

        // Search: nama / email
        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter kreator terpercaya
        if ($this->trustedFilter === 'trusted') {
            $query->where('is_trusted_creator', true);
        } elseif ($this->trustedFilter === 'non_trusted') {
            $query->where(function ($q) {
                $q->whereNull('is_trusted_creator')
                  ->orWhere('is_trusted_creator', false);
            });
        }

        // Filter payout terverifikasi / belum
        if ($this->payoutFilter === 'verified') {
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('payout_methods')
                  ->whereColumn('payout_methods.user_id', 'users.id')
                  ->where('payout_methods.status', 'verified');
            });
        } elseif ($this->payoutFilter === 'unverified') {
            $query->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('payout_methods')
                  ->whereColumn('payout_methods.user_id', 'users.id')
                  ->where('payout_methods.status', 'verified');
            });
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'most_revenue':
                $query->orderByDesc(
                    DB::raw('(select coalesce(sum(c.revenue_total),0) from contents c where c.user_id = users.id)')
                );
                break;

            case 'most_buyers':
                $query->orderByDesc(
                    DB::raw('(select coalesce(sum(c.buyers_count),0) from contents c where c.user_id = users.id)')
                );
                break;

            case 'most_contents':
                $query->orderByDesc(
                    DB::raw('(select count(*) from contents c where c.user_id = users.id)')
                );
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Ambil statistik konten untuk creator di halaman ini.
     */
    protected function loadContentStatsFor(array $creatorIds): array
    {
        if (empty($creatorIds)) {
            return [];
        }

        $rows = Content::query()
            ->selectRaw('user_id,
                count(*) as total_contents,
                sum(case when status = "published" then 1 else 0 end) as published_contents,
                sum(case when status = "draft" then 1 else 0 end) as draft_contents,
                sum(buyers_count) as total_buyers,
                sum(revenue_total) as total_revenue
            ')
            ->whereIn('user_id', $creatorIds)
            ->groupBy('user_id')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->user_id] = [
                'total_contents'     => (int) ($row->total_contents ?? 0),
                'published_contents' => (int) ($row->published_contents ?? 0),
                'draft_contents'     => (int) ($row->draft_contents ?? 0),
                'total_buyers'       => (int) ($row->total_buyers ?? 0),
                'total_revenue'      => (float) ($row->total_revenue ?? 0),
            ];
        }

        return $map;
    }

    /**
     * Ambil payout method default & verified untuk creator di halaman ini.
     */
    protected function loadPayoutPreviewFor(array $creatorIds): array
    {
        if (empty($creatorIds)) {
            return [];
        }

        $methods = PayoutMethod::query()
            ->whereIn('user_id', $creatorIds)
            ->where('status', 'verified')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

        $result = [];

        foreach ($methods as $userId => $list) {
            $method = $list->first(); // default verified (atau verified pertama)
            $masked = $this->maskNumber($method->account_number);

            $result[$userId] = [
                'label'  => $method->provider_name . ' • ' . $masked . ' a.n ' . $method->account_name,
                'status' => 'verified',
            ];
        }

        return $result;
    }

    protected function maskNumber(?string $number): ?string
    {
        if (! $number) {
            return null;
        }

        $plain = preg_replace('/\D+/', '', $number);
        if (strlen($plain) <= 4) {
            return $plain;
        }

        $first = substr($plain, 0, 4);
        return $first . '••••';
    }

    /**
     * Buka modal detail kreator.
     */
    public function openDetail(int $userId): void
    {
        $this->detailUserId    = $userId;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
    }

    public function render()
    {
        $query    = $this->baseQuery();
        $creators = $query->paginate($this->perPage);

        $creatorIds   = $creators->pluck('id')->all();
        $contentStats = $this->loadContentStatsFor($creatorIds);
        $payoutMap    = $this->loadPayoutPreviewFor($creatorIds);

        // Ringkasan atas: total kreator, aktif, trusted, dsb
        $totalCreators = User::where('role', 'creator')->count();
        $trustedCount  = User::where('role', 'creator')->where('is_trusted_creator', true)->count();

        // Untuk detail modal
        $detailUser   = null;
        $detailStat   = null;
        $detailPayout = null;

        if ($this->showDetailModal && $this->detailUserId) {
            $detailUser = User::where('role', 'creator')->find($this->detailUserId);

            if ($detailUser) {
                // Stat konten penuh user ini
                $statRow = Content::query()
                    ->selectRaw('
                        count(*) as total_contents,
                        sum(case when status = "published" then 1 else 0 end) as published_contents,
                        sum(case when status = "draft" then 1 else 0 end) as draft_contents,
                        sum(buyers_count) as total_buyers,
                        sum(revenue_total) as total_revenue
                    ')
                    ->where('user_id', $detailUser->id)
                    ->first();

                $detailStat = [
                    'total_contents'     => (int) ($statRow->total_contents ?? 0),
                    'published_contents' => (int) ($statRow->published_contents ?? 0),
                    'draft_contents'     => (int) ($statRow->draft_contents ?? 0),
                    'total_buyers'       => (int) ($statRow->total_buyers ?? 0),
                    'total_revenue'      => (float) ($statRow->total_revenue ?? 0),
                ];

                $pm = PayoutMethod::query()
                    ->where('user_id', $detailUser->id)
                    ->orderByDesc('is_default')
                    ->orderBy('id')
                    ->get();

                if ($pm->isNotEmpty()) {
                    $default = $pm->first();
                    $detailPayout = [
                        'provider' => $default->provider_name,
                        'account'  => $this->maskNumber($default->account_number),
                        'name'     => $default->account_name,
                        'status'   => $default->status,
                    ];
                }
            }
        }

        return view('livewire.admin.creators-index', [
            'creators'       => $creators,
            'contentStats'   => $contentStats,
            'payoutMap'      => $payoutMap,
            'totalCreators'  => $totalCreators,
            'trustedCount'   => $trustedCount,
            'detailUser'     => $detailUser,
            'detailStat'     => $detailStat,
            'detailPayout'   => $detailPayout,
        ]);
    }
}
