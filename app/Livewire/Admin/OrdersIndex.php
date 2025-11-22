<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrdersIndex extends Component
{
    use WithPagination;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    public string $search        = '';
    public string $statusFilter  = 'all';   // all | paid | pending | failed | refunded
    public string $paymentFilter = 'all';   // all | va | ewallet | transfer_bank | dsb
    public string $dateFilter    = '30d';   // all | today | 7d | 30d | 90d
    public string $sort          = 'latest';// latest | oldest | amount_high | amount_low

    protected $queryString = [
        'search'        => ['except' => ''],
        'statusFilter'  => ['except' => 'all'],
        'paymentFilter' => ['except' => 'all'],
        'dateFilter'    => ['except' => '30d'],
        'sort'          => ['except' => 'latest'],
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
        // Sesuaikan relasi dengan model Order milikmu
        return Order::query()
            ->with([
                'content',          // $order->content
                'content.user',     // kreator
                'user',             // pembeli
            ]);
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        if (! in_array($newStatus, ['pending', 'paid', 'failed', 'expired', 'refunded'], true)) {
            return;
        }

        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        $order->status = $newStatus;

        // kalau ditandai paid dan belum ada paid_at, set sekarang
        if ($newStatus === 'paid' && empty($order->paid_at)) {
            $order->paid_at = now();
        }

        $order->save();

        session()->flash(
            'status_admin_orders',
            "Status order #{$order->id} berhasil diubah menjadi {$newStatus}."
        );
    }

    public function render()
    {
        $query = $this->baseQuery();

        // Search: ID, kode order, nama pembeli, email, judul konten
        if ($this->search !== '') {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', $search) // kalau admin ketik ID langsung
                  ->orWhere('code', 'like', '%'.$search.'%') // kalau ada kolom code
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%'.$search.'%')
                          ->orWhere('email', 'like', '%'.$search.'%');
                  })
                  ->orWhereHas('content', function ($sub) use ($search) {
                      $sub->where('title', 'like', '%'.$search.'%');
                  });
            });
        }

        // Filter status
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'failed') {
                $query->whereIn('status', ['failed', 'expired']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        // Filter metode pembayaran
        if ($this->paymentFilter !== 'all') {
            $query->where('payment_method', $this->paymentFilter);
        }

        // Filter waktu
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

        // Sortir
        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                // ⬅️ pakai kolom `amount` (ganti kalau namanya beda)
                $query->orderBy('amount', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('amount', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $orders = $query->paginate(15);

        // Statistik global (tanpa filter)
        $totalAll     = Order::count();
        $totalPaid    = Order::where('status', 'paid')->count();
        $totalPending = Order::where('status', 'pending')->count();
        $totalFailed  = Order::whereIn('status', ['failed', 'expired'])->count();

        // ⬅️ DI SINI JUGA GANTI KE `amount`
        $totalRevenue = Order::where('status', 'paid')->sum('amount');

        return view('livewire.admin.orders-index', [
            'orders'       => $orders,
            'totalAll'     => $totalAll,
            'totalPaid'    => $totalPaid,
            'totalPending' => $totalPending,
            'totalFailed'  => $totalFailed,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
