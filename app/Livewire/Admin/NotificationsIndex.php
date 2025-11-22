<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsIndex extends Component
{
    use WithPagination;

    // === SIDEBAR STATE ===
    public bool $sidebarOpen = true;

    // === FILTER & STATE ===
    public string $tab    = 'all';     // all | unread | read
    public string $search = '';
    public string $sort   = 'latest';  // latest | oldest
    public int $perPage   = 15;

    protected $queryString = [
        'tab'    => ['except' => 'all'],
        'search' => ['except' => ''],
        'sort'   => ['except' => 'latest'],
        'page'   => ['except' => 1],
    ];

    protected $listeners = [
        'refreshNotifications' => '$refresh',
    ];

    // === SIDEBAR TOGGLE ===
    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTab(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    /**
     * Query dasar notifikasi admin yang sedang login
     */
    protected function baseQuery()
    {
        $admin = Auth::user();

        return DatabaseNotification::query()
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id);
    }

    public function render()
    {
        $admin = Auth::user();

        // --- Hitung statistik global ---
        $baseQuery = $this->baseQuery();

        $totalAll    = (clone $baseQuery)->count();
        $totalUnread = (clone $baseQuery)->whereNull('read_at')->count();
        $totalRead   = (clone $baseQuery)->whereNotNull('read_at')->count();

        // --- Query untuk list (pakai filter & sort) ---
        $query = $this->baseQuery();

        // Filter tab: all / unread / read
        if ($this->tab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->tab === 'read') {
            $query->whereNotNull('read_at');
        }

        // Search di data->title / data->message
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('data->title', 'like', "%{$search}%")
                    ->orWhere('data->message', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $notifications = $query->paginate($this->perPage);

        // unreadCount untuk badge (dipakai juga di header)
        $unreadCount = $totalUnread;

        return view('livewire.admin.notifications-index', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'totalAll'      => $totalAll,
            'totalUnread'   => $totalUnread,
            'totalRead'     => $totalRead,
        ]);
    }

    public function markAsRead(string $notificationId): void
    {
        $admin = Auth::user();

        $notification = DatabaseNotification::where('id', $notificationId)
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            session()->flash('status_admin_notifications', 'Notifikasi ditandai sudah dibaca.');
        }

        $this->dispatch('refreshNotifications');
    }

    public function markAllAsRead(): void
    {
        $admin = Auth::user();

        $this->baseQuery()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        session()->flash('status_admin_notifications', 'Semua notifikasi sudah ditandai dibaca.');
        $this->dispatch('refreshNotifications');
    }

    public function deleteNotification(string $notificationId): void
    {
        $admin = Auth::user();

        $notification = DatabaseNotification::where('id', $notificationId)
            ->where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->first();

        if ($notification) {
            $notification->delete();
            session()->flash('status_admin_notifications', 'Notifikasi dihapus.');
        }

        $this->dispatch('refreshNotifications');
    }

    public function deleteAllRead(): void
    {
        $this->baseQuery()
            ->whereNotNull('read_at')
            ->delete();

        session()->flash('status_admin_notifications', 'Semua notifikasi yang sudah dibaca dihapus.');
        $this->dispatch('refreshNotifications');
    }
}
