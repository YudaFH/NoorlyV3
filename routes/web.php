<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentPublicController;
use App\Http\Controllers\CreatorPublicController;

// Livewire ADMIN
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UsersIndex;
use App\Livewire\Admin\CreatorsIndex;
use App\Livewire\Admin\ContentReviewQueue;
use App\Livewire\Admin\ContentsIndex as AdminContentsIndex;
use App\Livewire\Admin\WithdrawsIndex; 
use App\Livewire\Admin\PayoutMethodsIndex;
use App\Livewire\Admin\NotificationsIndex;
use App\Livewire\Admin\Profile as AdminProfile;

// Livewire USER & CREATOR
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserPurchasesController;
use App\Http\Controllers\CreatorOnboardingController;
use App\Http\Controllers\UserSupportController;


use App\Livewire\Creator\Dashboard as CreatorDashboard;
use App\Livewire\Creator\Profile as CreatorProfile;
use App\Livewire\Creator\Balance as CreatorBalance;
use App\Livewire\Creator\ContentsIndex as CreatorContentsIndex;
use App\Livewire\Creator\ContentCreate;
use App\Livewire\Creator\ContentEdit;
use App\Livewire\Creator\EbookCreate;
use App\Livewire\Creator\Audience as CreatorAudience;
use App\Livewire\Creator\Support as CreatorSupport;
use App\Livewire\Creator\Notifications as CreatorNotifications;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/

Route::view('/', 'home')->name('home');
Route::view('/tentang', 'coming-soon')->name('tentang');

// Listing konten publik
Route::get('/konten', [ContentPublicController::class, 'index'])
    ->name('contents.index');

// Detail konten publik -> dipakai oleh route('contents.show', $content->slug)
Route::get('/konten/{slug}', [ContentPublicController::class, 'show'])
    ->name('contents.show');

Route::view('/pedoman-konten', 'legal.content-guidelines')->name('content.guidelines');

// Halaman publik kreator (bisa dishare ke audiens)
// Contoh URL: /kreator/5
Route::get('/kreator/{creator}', [CreatorPublicController::class, 'show'])
    ->name('creator.public.show');

// Checkout
Route::get('/checkout/{slug}', [CheckoutController::class, 'show'])
    ->name('checkout.show');
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
    ->name('checkout.pay');

// Halaman lain
Route::view('/komunitas', 'coming-soon')->name('komunitas');
Route::view('/acara', 'coming-soon')->name('acara');

Route::get('/kontak', [ContactController::class, 'index'])->name('contact.show');
Route::post('/kontak', [ContactController::class, 'send'])->name('contact.send');

// Halaman legal
Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');

/*
|--------------------------------------------------------------------------
| Auth guest (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register-page')->name('register');
    Route::view('/login', 'auth.login-page')->name('login');
});

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Area login (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard generik -> redirect sesuai role
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function (Request $request) {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'creator') {
            return redirect()->route('creator.dashboard');
        }

        // default: user biasa
        return redirect()->route('users.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | USER AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
    // Halaman profil & pengaturan
    Route::get('/profil', [UserProfileController::class, 'edit'])
        ->name('user.profile');

    // Update profil (nama + avatar)
    Route::patch('/profil', [UserProfileController::class, 'updateProfile'])
        ->name('user.profile.update');

    // Update password
    Route::patch('/profil/password', [UserProfileController::class, 'updatePassword'])
        ->name('user.profile.password');
    
    Route::get('/purchases', [UserPurchasesController::class, 'index'])
        ->name('user.purchases');

    Route::get('/jadi-kreator', [CreatorOnboardingController::class, 'index'])
        ->name('creators.onboarding');

    Route::post('/jadi-kreator', [CreatorOnboardingController::class, 'store'])
        ->name('creators.onboarding.submit');

    Route::get('/tiket-bantuan', [UserSupportController::class, 'index'])
        ->name('support.tickets.index');

    // Kirim tiket baru
    Route::post('/tiket-bantuan', [UserSupportController::class, 'store'])
        ->name('support.tickets.store');

    // Detail tiket + percakapan
    Route::get('/tiket-bantuan/{ticket}', [UserSupportController::class, 'show'])
        ->name('support.tickets.show');

    // Balas tiket (user reply)
    Route::post('/tiket-bantuan/{ticket}/reply', [UserSupportController::class, 'reply'])
        ->name('support.tickets.reply');
});

    /*
    |--------------------------------------------------------------------------
    | CREATOR AREA
    |--------------------------------------------------------------------------
    | Semua route kreator: /creator/...
    | Kena middleware: auth + role:creator
    */
    Route::prefix('creator')
        ->name('creator.')
        ->middleware('role:creator')
        ->group(function () {
            Route::get('/dashboard', CreatorDashboard::class)
                ->name('dashboard');

            Route::get('/profile', CreatorProfile::class)
                ->name('profile');

            Route::get('/balance', CreatorBalance::class)
                ->name('balance.index');

            Route::get('/audience',  CreatorAudience::class)
                ->name('audience.index');

            Route::get('/support',   CreatorSupport::class)
                ->name('support.index');

            Route::get('/notifications', CreatorNotifications::class)
                ->name('notifications.index');

            // Konten saya (khusus kreator)
            Route::get('/contents', CreatorContentsIndex::class)
                ->name('contents.index');

            Route::get('/contents/create', ContentCreate::class)
                ->name('contents.create');

            Route::get('/contents/{content}/edit', ContentEdit::class)
                ->name('contents.edit');

            // Halaman khusus buat e-book (builder)
            Route::get('/ebooks/create', EbookCreate::class)
                ->name('ebooks.create');
        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    | Semua route admin: /admin/...
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', AdminDashboard::class)
                ->name('dashboard');

            Route::get('/users', UsersIndex::class)
                ->name('users.index');

            Route::get('/creators', CreatorsIndex::class)
                ->name('creators.index');

            // Antrian review konten (moderasi)
            Route::get('/contents/review', ContentReviewQueue::class)
                ->name('contents.moderation');

            Route::get('/contents', AdminContentsIndex::class)
                ->name('contents.index');

            Route::get('/orders', \App\Livewire\Admin\OrdersIndex::class)
            ->name('orders.index');

            Route::get('/withdraws', WithdrawsIndex::class)->name('withdraws.index');

            Route::get('/payout-methods', PayoutMethodsIndex::class)->name('payout-methods.index');

            Route::get('/tickets', \App\Livewire\Admin\TicketsIndex::class)->name('tickets.index');
            Route::get('/tickets/{ticket}', \App\Livewire\Admin\TicketShow::class)->name('tickets.show');

            Route::get('/notifications', NotificationsIndex::class)
            ->name('notifications.index');

             Route::get('/settings', \App\Livewire\Admin\SettingsIndex::class)
            ->name('settings.index');

             Route::get('/profile', AdminProfile::class)->name('profile');
        });

    /*
    |--------------------------------------------------------------------------
    | React E-Book Builder
    |--------------------------------------------------------------------------
    */
    Route::view('/book/editor', 'book.editor')->name('book.editor');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Redirect jika ada yang akses *.blade.php langsung
|--------------------------------------------------------------------------
*/
Route::get('{path}.blade.php', function (string $path) {
    $clean = '/' . ltrim($path, '/');

    return redirect($clean, 301);
})->where('path', '.*');
