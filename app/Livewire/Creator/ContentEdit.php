<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Content;

class ContentEdit extends Component
{
    use WithFileUploads;

    protected string $layout = 'livewire.components.layouts.app';

    public bool $sidebarOpen = true;

    public Content $content;

    public string $title = '';
    public ?string $type = null;
    public $price = 0;

    // Media baru (optional)
    public $new_cover;         // image
    public $new_primary_file;  // pdf/zip/mp4/etc
    public ?string $primary_link_url = null;

    // E-book (struktur bab)
    public array $ebookChapters = [];

    public function mount(Content $content): void
    {
        $this->content = $content;

        $this->title            = $content->title;
        $this->type             = $content->type;
        $this->price            = $content->price;
        $this->primary_link_url = $content->primary_link_url;

        // Kalau konten ini e-book, isi builder dengan bab yang sudah tersimpan
        if ($content->type === 'ebook') {
            $this->ebookChapters = $content->ebook_chapters ?? [];

            if (empty($this->ebookChapters)) {
                $this->initDefaultChapters();
            }
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:191'],
            'type'             => ['nullable', 'string', 'max:100'],
            'price'            => ['nullable', 'numeric', 'min:0'],
            'new_cover'        => ['nullable', 'image', 'max:2048'],
            'new_primary_file' => ['nullable', 'file', 'max:51200'],
            'primary_link_url' => ['nullable', 'url', 'max:2048'],
            'ebookChapters'    => ['nullable', 'array'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'  => 'Judul konten wajib diisi.',
            'price.min'       => 'Harga minimal 0 (boleh 0 untuk konten gratis).',
            'new_cover.image' => 'Cover harus berupa gambar (JPG/PNG).',
        ];
    }

    /**
     * Inisialisasi bab default kalau e-book belum punya bab sama sekali.
     */
    protected function initDefaultChapters(): void
    {
        if (! empty($this->ebookChapters)) {
            return;
        }

        $this->ebookChapters = [
            [
                'id'    => uniqid('chap_'),
                'title' => 'Pendahuluan',
                'body'  => '',
            ],
        ];
    }

    /**
     * Dipanggil Livewire ketika tipe konten diubah dari UI.
     */
    public function updatedType($value): void
    {
        if ($value === 'ebook') {
            $this->initDefaultChapters();
        } else {
            // Kalau berubah jadi tipe lain, kosongkan builder e-book (opsional)
            $this->ebookChapters = [];
        }
    }

    /**
     * Tambah bab baru.
     */
    public function addChapter(): void
    {
        $this->ebookChapters[] = [
            'id'    => uniqid('chap_'),
            'title' => 'Bab baru',
            'body'  => '',
        ];
    }

    /**
     * Hapus bab berdasarkan ID unik.
     */
    public function removeChapter(string $id): void
    {
        $this->ebookChapters = collect($this->ebookChapters)
            ->reject(fn ($chap) => $chap['id'] === $id)
            ->values()
            ->all();
    }

    /**
     * Geser bab ke atas.
     */
    public function moveChapterUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $chapters = $this->ebookChapters;
        [$chapters[$index - 1], $chapters[$index]] = [$chapters[$index], $chapters[$index - 1]];
        $this->ebookChapters = array_values($chapters);
    }

    /**
     * Geser bab ke bawah.
     */
    public function moveChapterDown(int $index): void
    {
        if ($index >= count($this->ebookChapters) - 1) {
            return;
        }

        $chapters = $this->ebookChapters;
        [$chapters[$index + 1], $chapters[$index]] = [$chapters[$index], $chapters[$index + 1]];
        $this->ebookChapters = array_values($chapters);
    }

    /**
     * Validasi isi konten utama berdasarkan tipe.
     */
    protected function validateContentPayload(): bool
    {
        $hasFile = $this->new_primary_file || $this->content->primary_file_path;
        $hasLink = $this->primary_link_url;

        // EBOOK:
        // Bisa salah satu:
        // - file (lama atau baru), atau
        // - bab e-book yang diisi
        if ($this->type === 'ebook') {
            $hasChapters = count(
                array_filter($this->ebookChapters, function ($chap) {
                    $title = trim($chap['title'] ?? '');
                    $body  = trim($chap['body'] ?? '');
                    return $title !== '' || $body !== '';
                })
            ) > 0;

            if (! $hasFile && ! $hasChapters) {
                $this->addError(
                    'new_primary_file',
                    'Untuk e-book, isi minimal bab e-book atau pastikan ada file utama (misal PDF).'
                );
                return false;
            }
        }

        // TEMPLATE / BUNDLE: wajib file (lama atau baru)
        if (in_array($this->type, ['template', 'bundle'])) {
            if (! $hasFile) {
                $this->addError('new_primary_file', 'Untuk tipe ini kamu wajib mempunyai file utama (misal ZIP / file template).');
                return false;
            }
        }

        // VIDEO / WEBINAR: minimal file (lama/baru) atau link
        if (in_array($this->type, ['video', 'webinar'])) {
            if (! $hasFile && ! $hasLink) {
                $this->addError('primary_link_url', 'Untuk video / webinar, isi konten minimal link video atau upload file.');
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

        $user = Auth::user();
        if (! $user || $user->id !== $this->content->user_id) {
            abort(403);
        }

        // Update field dasar
        $this->content->title            = $this->title;
        $this->content->type             = $this->type;
        $this->content->price            = $this->price ?: 0;
        $this->content->primary_link_url = $this->primary_link_url;

        // Kalau tipe = ebook, simpan struktur bab
        if ($this->type === 'ebook') {
            $this->content->ebook_chapters = $this->ebookChapters;
        } else {
            $this->content->ebook_chapters = null;
        }

        // Cover baru (hapus yang lama kalau ada)
        if ($this->new_cover) {
            if ($this->content->cover_path) {
                Storage::disk('public')->delete($this->content->cover_path);
            }

            $this->content->cover_path = $this->new_cover->store('contents/covers', 'public');
        }

        // File utama baru
        if ($this->new_primary_file) {
            if ($this->content->primary_file_path) {
                Storage::disk('public')->delete($this->content->primary_file_path);
            }

            $this->content->primary_file_path = $this->new_primary_file->store('contents/files', 'public');
        }

        // Catatan: status tidak diubah di sini (tetap draft/pending/published sesuai sekarang).
        $this->content->save();

        session()->flash('status_contents', 'Konten berhasil diperbarui.');

        redirect()->route('creator.contents.index');
    }

    public function render()
    {
        return view('livewire.creator.content-edit', [
            'content' => $this->content,
        ]);
    }
}
