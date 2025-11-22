<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Content;

class ContentsIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';

    public bool $sidebarOpen = true;

    public string $search        = '';
    public string $statusFilter  = 'all';   // all|draft|published|pending_review|archived
    public string $typeFilter    = 'all';   // all|ebook|video|...
    public string $priceFilter   = 'all';   // all|free|paid
    public string $sort          = 'latest';// latest|oldest|buyers_desc|revenue_desc|views_desc
    public int    $perPage       = 15;

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'typeFilter'   => ['except' => 'all'],
        'priceFilter'  => ['except' => 'all'],
        'sort'         => ['except' => 'latest'],
        'page'         => ['except' => 1],
    ];

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    // Reset pagination kalau filter berubah
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    /**
     * Ubah status konten (draft/published/archived).
     */
    public function updateStatus(int $contentId, string $status): void
    {
        $allowed = ['draft', 'published', 'archived', 'pending_review'];

        if (! in_array($status, $allowed, true)) {
            return;
        }

        $content = Content::find($contentId);
        if (! $content) {
            return;
        }

        $content->status = $status;
        $content->save();

        session()->flash('status_admin_contents', 'Status konten berhasil diperbarui.');
    }

    /**
     * Hapus konten secara permanen.
     */
    public function delete(int $contentId): void
    {
        $content = Content::find($contentId);
        if (! $content) {
            return;
        }

        // NOTE: kalau mau, di sini bisa sekalian hapus file dari storage.
        $content->delete();

        session()->flash('status_admin_contents', 'Konten berhasil dihapus.');
        $this->resetPage();
    }

    public function applyFilters(): void
    {

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }


    public function render()
    {
        $query = Content::query()->with('user');

        // Filter: search judul / nama kreator
        if ($this->search !== '') {
            $search = '%'.$this->search.'%';

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', $search)
                         ->orWhere('email', 'like', $search);
                  });
            });
        }

        // Filter status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filter tipe konten
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Filter harga
        if ($this->priceFilter === 'free') {
            $query->where(function ($q) {
                $q->whereNull('price')
                  ->orWhere('price', 0);
            });
        } elseif ($this->priceFilter === 'paid') {
            $query->where('price', '>', 0);
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'buyers_desc':
                $query->orderByDesc('buyers_count');
                break;
            case 'revenue_desc':
                $query->orderByDesc('revenue_total');
                break;
            case 'views_desc':
                $query->orderByDesc('views_count');
                break;
            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $contents = $query->paginate($this->perPage);

        // Statistik singkat
        $totalAll      = Content::count();
        $totalPublished = Content::where('status', 'published')->count();
        $totalDraft     = Content::where('status', 'draft')->count();
        $totalPending   = Content::where('status', 'pending_review')->count();
        $totalArchived  = Content::where('status', 'archived')->count();

        return view('livewire.admin.contents-index', [
            'contents'       => $contents,
            'totalAll'       => $totalAll,
            'totalPublished' => $totalPublished,
            'totalDraft'     => $totalDraft,
            'totalPending'   => $totalPending,
            'totalArchived'  => $totalArchived,
        ]);
    }
}
