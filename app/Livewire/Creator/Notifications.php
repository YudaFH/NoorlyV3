<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;

class Notifications extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    public string $typeFilter = 'all'; // all | balance | content | buyer | support | system
    public bool $onlyUnread = false;
    public string $search = '';

    protected array $typeLabels = [
        'balance' => 'Saldo & penarikan',
        'content' => 'Konten',
        'buyer'   => 'Pembeli',
        'support' => 'Support',
        'system'  => 'Platform',
    ];

    public function getTypeOptionsProperty(): array
    {
        return $this->typeLabels;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOnlyUnread(): void
    {
        $this->resetPage();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function baseQuery()
    {
        $user = Auth::user();

        if (! $user) {
            return UserNotification::query()->whereRaw('1 = 0');
        }

        $query = UserNotification::where('user_id', $user->id);

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->onlyUnread) {
            $query->whereNull('read_at');
        }

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('body', 'like', $search);
            });
        }

        return $query;
    }

    public function markAsRead(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $notif = UserNotification::where('user_id', $user->id)->find($id);

        if ($notif && is_null($notif->read_at)) {
            $notif->read_at = now();
            $notif->save();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function deleteNotification(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        UserNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $base = $this->baseQuery();

        $totalAll   = $user
            ? UserNotification::where('user_id', $user->id)->count()
            : 0;
        $totalUnread = $user
            ? UserNotification::where('user_id', $user->id)->whereNull('read_at')->count()
            : 0;

        $balanceCount = $user
            ? UserNotification::where('user_id', $user->id)->where('type', 'balance')->count()
            : 0;
        $contentCount = $user
            ? UserNotification::where('user_id', $user->id)->where('type', 'content')->count()
            : 0;
        $buyerCount   = $user
            ? UserNotification::where('user_id', $user->id)->where('type', 'buyer')->count()
            : 0;
        $supportCount = $user
            ? UserNotification::where('user_id', $user->id)->where('type', 'support')->count()
            : 0;
        $systemCount  = $user
            ? UserNotification::where('user_id', $user->id)->where('type', 'system')->count()
            : 0;

        $notifications = $base
            // Unread dulu (read_at NULL) baru yang sudah dibaca
            ->orderByRaw('read_at IS NULL DESC')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('livewire.creator.notifications', [
            'notifications' => $notifications,
            'totalAll'      => $totalAll,
            'totalUnread'   => $totalUnread,
            'balanceCount'  => $balanceCount,
            'contentCount'  => $contentCount,
            'buyerCount'    => $buyerCount,
            'supportCount'  => $supportCount,
            'systemCount'   => $systemCount,
        ]);
    }
}
