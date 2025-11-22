<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Content;
use App\Models\WithdrawRequest;
use App\Models\Order;          // sesuaikan dengan model order kamu

class Dashboard extends Component
{
    public bool $sidebarOpen = true;

    protected string $layout = 'livewire.components.layouts.app';

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function render()
    {
        // Angka utama
        $totalUsers      = User::count();
        $totalCreators   = User::where('role', 'creator')->count();
        $totalContents   = Content::count();
        $pendingContents = Content::where('status', 'pending_review')->count();

        $totalRevenue = Order::where('status', 'paid')->sum('amount'); // sesuaikan field
        $pendingWithdrawAmount = WithdrawRequest::where('status', 'pending')->sum('amount');
        $pendingWithdrawCount  = WithdrawRequest::where('status', 'pending')->count();

        // Aktivitas terbaru
        $recentCreators = User::where('role', 'creator')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentPendingContents = Content::where('status', 'pending_review')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentWithdraws = WithdrawRequest::where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalUsers'            => $totalUsers,
            'totalCreators'         => $totalCreators,
            'totalContents'         => $totalContents,
            'pendingContents'       => $pendingContents,
            'totalRevenue'          => $totalRevenue,
            'pendingWithdrawAmount' => $pendingWithdrawAmount,
            'pendingWithdrawCount'  => $pendingWithdrawCount,
            'recentCreators'        => $recentCreators,
            'recentPendingContents' => $recentPendingContents,
            'recentWithdraws'       => $recentWithdraws,
        ]);
    }
}
