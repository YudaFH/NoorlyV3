<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Content;
use App\Models\User;
use Illuminate\Http\Request;

class UserPurchasesController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // Ambil semua order milik user yg statusnya sudah "lunas"
        // (silakan sesuaikan dengan nilai status di DB kamu)
        $orders = Order::with('content')
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'paid',        // kalau kamu pakai 'paid'
                'completed',   // atau 'completed'
                'success',     // atau 'success'
                'settlement',  // kalau ikut nama dari Midtrans
            ])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.purchases', [
            'user'   => $user,
            'orders' => $orders,
        ]);
    }
}
