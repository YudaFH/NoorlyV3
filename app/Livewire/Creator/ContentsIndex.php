<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Content;

class ContentsIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    // Filter / search / sort
    public string $statusFilter = 'all';     // all, draft, published, scheduled, archived, pending_review
    public string $search       = '';
    public string $sort         = 'latest'; // latest, oldest, most_buyers, most_revenue, most_views

    // Untuk reset page kalau filter/search berubah
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function query()
    {
        $user = Auth::user();

        $query = Content::query()
            ->where('user_id', $user->id ?? null);

        // Filter status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Search judul / slug
        if ($this->search !== '') {
            $search = '%' . $this->search . '%';

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('slug', 'like', $search);
            });
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_buyers':
                $query->orderByDesc('buyers_count');
                break;
            case 'most_revenue':
                $query->orderByDesc('revenue_total');
                break;
            case 'most_views':
                $query->orderByDesc('views_count');
                break;
            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        return $query;
    }

    public function deleteContent(int $contentId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $content = Content::where('id', $contentId)
            ->where('user_id', $user->id)
            ->first();

        if (! $content) {
            return;
        }

        // Kalau mau soft delete bisa pakai $content->update(['status' => 'deleted']);
        $content->delete();

        session()->flash('status_contents', 'Konten berhasil dihapus.');

        // refresh list
        $this->resetPage(); // kalau pakai WithPagination
    }


    public function render()
    {
        $user = Auth::user();

        $contents = $this->query()->paginate(8);

        // Ringkasan kecil
        $totalAll      = Content::where('user_id', $user->id ?? null)->count();
        $totalDraft    = Content::where('user_id', $user->id ?? null)->where('status', 'draft')->count();
        $totalActive   = Content::where('user_id', $user->id ?? null)->where('status', 'published')->count();
        $totalArchived = Content::where('user_id', $user->id ?? null)->where('status', 'archived')->count();

        $topContent = Content::where('user_id', $user->id ?? null)
            ->where('status', 'published')
            ->orderByDesc('buyers_count')
            ->first();

        return view('livewire.creator.contents-index', [
            'contents'      => $contents,
            'totalAll'      => $totalAll,
            'totalDraft'    => $totalDraft,
            'totalActive'   => $totalActive,
            'totalArchived' => $totalArchived,
            'topContent'    => $topContent,
        ]);
    }
}
