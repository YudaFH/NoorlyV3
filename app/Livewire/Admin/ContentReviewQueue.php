<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Content;

class ContentReviewQueue extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    public string $search = '';
    public string $typeFilter = 'all';      // all | ebook | video | webinar | template | bundle | other
    public string $postReviewFilter = 'all'; // all | publish | draft
    public string $sort = 'newest';         // newest | oldest | price_high | price_low

    protected $queryString = [
        'search'           => ['except' => ''],
        'typeFilter'       => ['except' => 'all'],
        'postReviewFilter' => ['except' => 'all'],
        'sort'             => ['except' => 'newest'],
    ];

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPostReviewFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    /**
     * Setujui konten → status berubah:
     * - kalau post_review_action = 'draft' → status = 'draft'
     * - selain itu / null → status = 'published'
     */
    public function approve(int $contentId): void
    {
        $content = Content::where('id', $contentId)
            ->where('status', 'pending_review')
            ->first();

        if (! $content) {
            session()->flash('status_admin_review', 'Konten sudah tidak berada di antrian review.');
            return;
        }

        $targetStatus = $content->post_review_action === 'draft'
            ? 'draft'
            : 'published';

        $content->status = $targetStatus;
        $content->save();

        session()->flash(
            'status_admin_review',
            'Konten "'.$content->title.'" disetujui dan sekarang berstatus '.
            ($targetStatus === 'published' ? 'Terbit.' : 'Draft.')
        );
    }

    /**
     * Tolak konten → dipaksa balik ke draft.
     * (Untuk alasan / catatan, nanti bisa ditambah kolom baru kalau mau.)
     */
    public function reject(int $contentId): void
    {
        $content = Content::where('id', $contentId)
            ->where('status', 'pending_review')
            ->first();

        if (! $content) {
            session()->flash('status_admin_review', 'Konten sudah tidak berada di antrian review.');
            return;
        }

        $content->status = 'draft';
        // Optional: supaya kalau di-review ulang default-nya ke draft lagi
        $content->post_review_action = 'draft';
        $content->save();

        session()->flash(
            'status_admin_review',
            'Konten "'.$content->title.'" dikembalikan ke draft. Beri tahu kreator jika butuh revisi.'
        );
    }

    public function render()
    {
        $base = Content::query()
            ->with('user')
            ->where('status', 'pending_review');

        // statistik kecil
        $totalPending = (clone $base)->count();

        $query = clone $base;

        // Search
        if ($this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%'.$s.'%')
                  ->orWhereHas('user', function ($sub) use ($s) {
                      $sub->where('name', 'like', '%'.$s.'%')
                          ->orWhere('email', 'like', '%'.$s.'%');
                  });
            });
        }

        // Filter tipe
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Filter preferensi setelah review
        if ($this->postReviewFilter === 'publish') {
            $query->where(function ($q) {
                $q->whereNull('post_review_action')
                  ->orWhere('post_review_action', 'publish');
            });
        } elseif ($this->postReviewFilter === 'draft') {
            $query->where('post_review_action', 'draft');
        }

        // Sort
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $contents = $query->paginate(10);

        return view('livewire.admin.content-review-queue', [
            'contents'     => $contents,
            'totalPending' => $totalPending,
        ]);
    }
}
