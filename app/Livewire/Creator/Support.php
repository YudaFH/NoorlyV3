<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;

class Support extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected string $layout = 'livewire.components.layouts.app';
    protected $paginationTheme = 'tailwind';

    public bool $sidebarOpen = true;

    // Form tiket baru
    public ?string $category = null;
    public ?string $subject = null;
    public ?string $message = null;
    public $attachment; // file upload

    // Filter list tiket
    public string $statusFilter = 'all'; // all | open | in_progress | resolved | closed
    public string $search = '';

    protected array $categories = [
        'pembayaran'      => 'Masalah pembayaran',
        'penarikan'       => 'Penarikan saldo',
        'konten'          => 'Konten & review',
        'akses_pembeli'   => 'Akses pembeli',
        'bug'             => 'Bug di dashboard',
        'saran'           => 'Saran fitur / feedback',
        'lainnya'         => 'Lainnya',
    ];

    public function getCategoryOptionsProperty(): array
    {
        return $this->categories;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function rules(): array
    {
        return [
            'category'   => ['required', 'string', 'max:100'],
            'subject'    => ['required', 'string', 'max:191'],
            'message'    => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function submitTicket(): void
    {
        $this->validate();

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $attachmentPath = null;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('support_attachments', 'public');
        }

        SupportTicket::create([
            'user_id'         => $user->id,
            'category'        => $this->category,
            'subject'         => $this->subject,
            'message'         => $this->message,
            'status'          => 'open',
            'attachment_path' => $attachmentPath,
        ]);

        // Reset form
        $this->reset(['category', 'subject', 'message', 'attachment']);

        session()->flash('support_status', 'Tiket support berhasil dikirim. Tim Noorly akan merespon maksimal 1x24 jam.');
        $this->resetPage();
    }

    protected function baseQuery()
    {
        $user = Auth::user();
        if (! $user) {
            return SupportTicket::query()->whereRaw('1 = 0');
        }

        $query = SupportTicket::where('user_id', $user->id);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', $search)
                  ->orWhere('message', 'like', $search);
            });
        }

        return $query;
    }

    public function render()
    {
        $user = Auth::user();

        $base = $this->baseQuery();

        $totalTickets = (clone $base)->count();
        $openTickets  = (clone $base)->where('status', 'open')->count();
        $inProgress   = (clone $base)->where('status', 'in_progress')->count();
        $resolved     = (clone $base)->whereIn('status', ['resolved', 'closed'])->count();

        $tickets = $base->orderByDesc('created_at')->paginate(10);

        return view('livewire.creator.support', [
            'tickets'      => $tickets,
            'totalTickets' => $totalTickets,
            'openTickets'  => $openTickets,
            'inProgress'   => $inProgress,
            'resolved'     => $resolved,
        ]);
    }
}
