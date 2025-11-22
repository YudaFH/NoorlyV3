<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Content;
use Illuminate\Support\Facades\Auth;

class UsersIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';

    public bool $sidebarOpen = true;

    // Yang dipakai untuk query (filter aktif)
    public ?string $roleFilter = null;    // user, creator, admin
    public ?string $statusFilter = null;  // active, suspended, null
    public string $search = '';
    public string $sort = 'latest';       // latest, oldest, name_asc, name_desc

    // Input di UI (baru aktif kalau klik "Terapkan")
    public ?string $filterRoleInput = null;
    public ?string $filterStatusInput = null;
    public string $filterSearchInput = '';
    public string $filterSortInput = 'latest';

    // Detail modal
    public ?int $detailUserId = null;
    public ?User $detailUser = null;
    public array $detailStats = [];
    public bool $showDetailModal = false;

    protected $queryString = [
        'roleFilter'   => ['except' => null],
        'statusFilter' => ['except' => null],
        'search'       => ['except' => ''],
        'sort'         => ['except' => 'latest'],
        'page'         => ['except' => 1],
    ];

    public function mount(): void
    {
        // Sinkron awal: input UI = filter aktif
        $this->filterRoleInput   = $this->roleFilter;
        $this->filterStatusInput = $this->statusFilter;
        $this->filterSearchInput = $this->search;
        $this->filterSortInput   = $this->sort;
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    /**
     * Tombol "Terapkan" filter
     */
    public function applyFilters(): void
    {
        $this->roleFilter   = $this->filterRoleInput ?: null;
        $this->statusFilter = $this->filterStatusInput ?: null;
        $this->search       = trim($this->filterSearchInput ?? '');
        $this->sort         = $this->filterSortInput ?: 'latest';

        $this->resetPage();
    }

    /**
     * Helper: cek boleh nggak ubah role user.
     */
    protected function canChangeRole(User $target, string $newRole): bool
    {
        /** @var User|null $admin */
        $admin = Auth::user();

        if (! $admin || $admin->role !== 'admin') {
            session()->flash('status_error', 'Hanya admin yang boleh mengubah role pengguna.');
            return false;
        }

        // Tidak boleh menghapus role admin dari diri sendiri
        if ($admin->id === $target->id && $newRole !== 'admin') {
            session()->flash('status_error', 'Kamu tidak bisa menghapus role admin dari akunmu sendiri.');
            return false;
        }

        // Tidak boleh mengurangi admin terakhir
        if ($target->role === 'admin' && $newRole !== 'admin') {
            $adminsCount = User::where('role', 'admin')->count();
            if ($adminsCount <= 1) {
                session()->flash('status_error', 'Minimal harus ada satu admin aktif di Noorly.');
                return false;
            }
        }

        return true;
    }

    public function setRole(int $userId, string $role): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        if (! in_array($role, ['user', 'creator', 'admin'], true)) {
            return;
        }

        if (! $this->canChangeRole($user, $role)) {
            return;
        }

        $user->role = $role;

        // Kalau bukan kreator, cabut trusted_creator
        if ($role !== 'creator' && isset($user->is_trusted_creator)) {
            $user->is_trusted_creator = false;
        }

        $user->save();

        session()->flash('status_users', "Role {$user->name} diubah menjadi {$role}.");
    }

    public function toggleTrustedCreator(int $userId): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        if ($user->role !== 'creator') {
            session()->flash('status_error', 'Status kreator terpercaya hanya untuk akun dengan role kreator.');
            return;
        }

        if (! isset($user->is_trusted_creator)) {
            session()->flash('status_error', 'Kolom is_trusted_creator belum tersedia di tabel users.');
            return;
        }

        $user->is_trusted_creator = ! (bool) $user->is_trusted_creator;
        $user->save();

        session()->flash(
            'status_users',
            $user->is_trusted_creator
                ? "{$user->name} sekarang ditandai sebagai kreator terpercaya."
                : "Status kreator terpercaya untuk {$user->name} telah dicabut."
        );
    }

    public function toggleSuspend(int $userId): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        /** @var User|null $admin */
        $admin = Auth::user();
        if (! $admin || $admin->role !== 'admin') {
            session()->flash('status_error', 'Hanya admin yang boleh menangguhkan akun.');
            return;
        }

        // Jangan suspend diri sendiri
        if ($admin->id === $user->id) {
            session()->flash('status_error', 'Kamu tidak bisa menangguhkan akunmu sendiri.');
            return;
        }

        // Jangan suspend admin terakhir
        if ($user->role === 'admin') {
            $adminsCount = User::where('role', 'admin')->count();
            if ($adminsCount <= 1) {
                session()->flash('status_error', 'Tidak bisa menangguhkan satu-satunya admin yang tersisa.');
                return;
            }
        }

        if (! isset($user->is_suspended)) {
            session()->flash('status_error', 'Kolom is_suspended belum tersedia di tabel users.');
            return;
        }

        $user->is_suspended = ! (bool) $user->is_suspended;
        $user->save();

        session()->flash(
            'status_users',
            $user->is_suspended
                ? "Akun {$user->name} telah ditangguhkan."
                : "Akun {$user->name} telah diaktifkan kembali."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL PENGGUNA
    |--------------------------------------------------------------------------
    */
    public function openDetail(int $userId): void
    {
        $user = User::find($userId);
        if (! $user) {
            return;
        }

        $this->detailUserId = $userId;
        $this->detailUser   = $user;

        // Statistik konten kreator
        $contentsTotal     = Content::where('user_id', $userId)->count();
        $contentsPublished = Content::where('user_id', $userId)->where('status', 'published')->count();
        $contentsDraft     = Content::where('user_id', $userId)->where('status', 'draft')->count();
        $contentsPending   = Content::where('user_id', $userId)->where('status', 'pending_review')->count();

        $revenueTotal      = Content::where('user_id', $userId)->sum('revenue_total');
        $buyersTotal       = Content::where('user_id', $userId)->sum('buyers_count');

        $this->detailStats = [
            'contents_total'     => $contentsTotal,
            'contents_published' => $contentsPublished,
            'contents_draft'     => $contentsDraft,
            'contents_pending'   => $contentsPending,
            'revenue_total'      => $revenueTotal,
            'buyers_total'       => $buyersTotal,
        ];

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailUserId    = null;
        $this->detailUser      = null;
        $this->detailStats     = [];
    }

    public function render()
    {
        $query = User::query();

        // Filter role (yang sudah diterapkan)
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Filter status akun (aktif / suspended)
        if ($this->statusFilter === 'active') {
            if (schema_has_column('users', 'is_suspended') ?? true) {
                $query->where(function ($q) {
                    $q->whereNull('is_suspended')
                      ->orWhere('is_suspended', false);
                });
            }
        } elseif ($this->statusFilter === 'suspended') {
            if (schema_has_column('users', 'is_suspended') ?? true) {
                $query->where('is_suspended', true);
            }
        }

        // Search (yang sudah diterapkan)
        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Sorting
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(15);

        $totalUsers    = User::count();
        $totalCreators = User::where('role', 'creator')->count();
        $totalAdmins   = User::where('role', 'admin')->count();
        $totalMembers  = User::where('role', 'user')->count();

        return view('livewire.admin.users-index', [
            'users'         => $users,
            'totalUsers'    => $totalUsers,
            'totalCreators' => $totalCreators,
            'totalAdmins'   => $totalAdmins,
            'totalMembers'  => $totalMembers,
        ]);
    }
}

/**
 * Helper kecil untuk cek kolom tanpa bikin error kalau nggak ada.
 */
if (! function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
