<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Content;
use App\Models\PayoutMethod;
use App\Models\User;

class ContentCreate extends Component
{
    use WithFileUploads;

    protected string $layout = 'livewire.components.layouts.app';

    public bool $sidebarOpen = true;

    // Data utama konten
    public string $title = '';
    public string $description = '';
    public ?string $type = null;       // ebook, video, webinar, dll
    public $price = 0;

    // Media
    public $cover;                     // image
    public $primary_file;              // pdf, zip, mp4, dll
    public ?string $primary_link_url = null;

    // Pedoman konten
    public bool $accept_guidelines = false;

    // Untuk kreator baru: setelah review, mau langsung terbit atau masuk draft dulu
    public ?string $post_review_action = 'publish'; // 'publish' atau 'draft'

    // Info penarikan / payout
    public bool $canCreatePaidContent = false;
    public ?string $defaultPayoutLabel = null;
    public int $platformFeePercent = 10; // misal fee platform 10%

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // Cek apakah user punya metode penarikan yang sudah diverifikasi & default
        $method = PayoutMethod::where('user_id', $user->id)
            ->where('status', 'verified')
            ->where('is_default', true)
            ->first();

        if ($method) {
            $this->canCreatePaidContent = true;
            $masked = $this->maskNumber($method->account_number);
            $this->defaultPayoutLabel = $method->provider_name.' - '.$masked.' a.n '.$method->account_name;
        } else {
            $this->canCreatePaidContent = false;
            $this->defaultPayoutLabel = null;
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:191'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'type'              => ['nullable', 'string', 'max:100'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'cover'             => ['nullable', 'image', 'max:2048'],       // ~2MB
            'primary_file'      => ['nullable', 'file', 'max:51200'],       // ~50MB
            'primary_link_url'  => ['nullable', 'url', 'max:2048'],
            'accept_guidelines' => ['accepted'],
            'post_review_action'=> ['nullable', 'in:publish,draft'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'             => 'Judul konten wajib diisi.',
            'price.min'                  => 'Harga minimal 0 (boleh 0 untuk konten gratis).',
            'cover.image'                => 'Cover harus berupa gambar (JPG/PNG).',
            'cover.max'                  => 'Ukuran cover maksimal sekitar 2MB.',
            'primary_file.max'           => 'Ukuran file maksimal sekitar 50MB. Untuk video panjang, disarankan upload ke YouTube / Google Drive lalu tempel link.',
            'accept_guidelines.accepted' => 'Kamu harus menyetujui pedoman konten Noorly terlebih dahulu.',
        ];
    }

    protected function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'konten';
        }

        $slug = $base;
        $counter = 1;

        while (Content::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Masking nomor rekening / e-wallet.
     */
    protected function maskNumber(?string $number): string
    {
        if (! $number) {
            return '-';
        }

        $number = preg_replace('/\s+/', '', $number);
        $len = strlen($number);

        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        $last4 = substr($number, -4);
        return str_repeat('*', $len - 4).$last4;
    }

    /**
     * Validasi isi konten utama berdasar tipe.
     */
    protected function validateContentPayload(): bool
    {
        $hasFile = (bool) $this->primary_file;
        $hasLink = (bool) $this->primary_link_url;

        // TEMPLATE / BUNDLE / EBOOK: wajib file
        if (in_array($this->type, ['ebook', 'template', 'bundle'], true)) {
            if (! $hasFile) {
                $this->addError(
                    'primary_file',
                    'Untuk e-book / template / bundle, kamu wajib mengunggah file utama (misal PDF / ZIP).'
                );
                return false;
            }
        }

        // VIDEO / WEBINAR: minimal file ATAU link
        if (in_array($this->type, ['video', 'webinar'], true)) {
            if (! $hasFile && ! $hasLink) {
                $this->addError(
                    'primary_link_url',
                    'Untuk video / webinar, isi konten minimal link video atau upload file.'
                );
                return false;
            }
        }

        return true;
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->validateContentPayload()) {
            return;
        }

        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // Kalau mau kasih harga > 0 tapi payout belum verified → tolak
        if (($this->price ?? 0) > 0 && ! $this->canCreatePaidContent) {
            $this->addError(
                'price',
                'Kamu belum bisa membuat konten berbayar karena belum ada metode penarikan yang terverifikasi. Ajukan metode penarikan dulu di halaman "Saldo & penarikan".'
            );
            return;
        }

        $status = $user->is_trusted_creator ? 'published' : 'pending_review';
        $slug   = $this->generateUniqueSlug($this->title);

        // Simpan file ke storage
        $coverPath = $this->cover
            ? $this->cover->store('contents/covers', 'public')
            : null;

        $filePath = $this->primary_file
            ? $this->primary_file->store('contents/files', 'public')
            : null;

        // Simpan konten
        $content = Content::create([
            'user_id'           => $user->id,
            'title'             => $this->title,
            'slug'              => $slug,
            'description'       => $this->description ?: null,
            'type'              => $this->type,
            'status'            => $status,
            'post_review_action'=> $user->is_trusted_creator ? null : ($this->post_review_action ?: 'publish'),
            'price'             => $this->price ?: 0,
            'views_count'       => 0,
            'buyers_count'      => 0,
            'revenue_total'     => 0,
            'cover_path'        => $coverPath,
            'primary_file_path' => $filePath,
            'primary_link_url'  => $this->primary_link_url,
        ]);

        session()->flash(
            'status_contents',
            $status === 'pending_review'
                ? 'Konten berhasil dibuat dan sedang menunggu review admin Noorly.'
                : 'Konten berhasil dibuat dan sudah terbit.'
        );

        redirect()->route('creator.contents.index');
    }

    public function render()
    {
        return view('livewire.creator.content-create');
    }
}
