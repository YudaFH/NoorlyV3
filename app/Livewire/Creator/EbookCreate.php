<?php

namespace App\Livewire\Creator;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Content;

class EbookCreate extends Component
{
    use WithFileUploads;

    protected string $layout = 'livewire.components.layouts.app';

    public bool $sidebarOpen = true;

    public string $title = '';
    public $price = 0;

    public $cover; // image
    public $primary_file; // PDF opsional

    public array $ebookChapters = [];

    public bool $accept_guidelines = false;

    public function mount(): void
    {
        $this->initDefaultChapters();
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    protected function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:191'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'cover'             => ['nullable', 'image', 'max:2048'],  // ~2MB
            'primary_file'      => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // ~20MB
            'ebookChapters'     => ['required', 'array', 'min:1'],
            'accept_guidelines' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'         => 'Judul e-book wajib diisi.',
            'price.min'              => 'Harga minimal 0 (boleh 0 untuk e-book gratis).',
            'cover.image'            => 'Cover harus berupa gambar (JPG/PNG).',
            'primary_file.mimes'     => 'File utama e-book harus berupa PDF.',
            'primary_file.max'       => 'Ukuran file PDF maksimal sekitar 20MB.',
            'ebookChapters.required' => 'E-book harus memiliki minimal 1 bab.',
            'ebookChapters.min'      => 'E-book harus memiliki minimal 1 bab.',
            'accept_guidelines.accepted' => 'Kamu harus menyetujui pedoman konten Noorly terlebih dahulu.',
        ];
    }

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

    protected function validateChapters(): bool
    {
        $filled = collect($this->ebookChapters)
            ->filter(function ($chap) {
                $title = trim($chap['title'] ?? '');
                $body  = trim($chap['body'] ?? '');
                return $title !== '' || $body !== '';
            });

        if ($filled->isEmpty()) {
            $this->addError('ebookChapters', 'Isi minimal satu bab e-book (judul atau isi bab tidak boleh kosong semua).');
            return false;
        }

        return true;
    }

    public function addChapter(): void
    {
        $this->ebookChapters[] = [
            'id'    => uniqid('chap_'),
            'title' => 'Bab baru',
            'body'  => '',
        ];
    }

    public function removeChapter(string $id): void
    {
        $this->ebookChapters = collect($this->ebookChapters)
            ->reject(fn ($chap) => ($chap['id'] ?? null) === $id)
            ->values()
            ->all();
    }

    public function moveChapterUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $chapters = $this->ebookChapters;
        [$chapters[$index - 1], $chapters[$index]] = [$chapters[$index], $chapters[$index - 1]];
        $this->ebookChapters = array_values($chapters);
    }

    public function moveChapterDown(int $index): void
    {
        if ($index >= count($this->ebookChapters) - 1) {
            return;
        }

        $chapters = $this->ebookChapters;
        [$chapters[$index + 1], $chapters[$index]] = [$chapters[$index], $chapters[$index + 1]];
        $this->ebookChapters = array_values($chapters);
    }

    protected function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'ebook';
        }

        $slug = $base;
        $counter = 1;

        while (Content::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->validateChapters()) {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $status = $user->is_trusted_creator ? 'published' : 'pending_review';
        $slug   = $this->generateUniqueSlug($this->title);

        $coverPath = $this->cover
            ? $this->cover->store('contents/covers', 'public')
            : null;

        $filePath = $this->primary_file
            ? $this->primary_file->store('contents/files', 'public')
            : null;

        Content::create([
            'user_id'           => $user->id,
            'title'             => $this->title,
            'slug'              => $slug,
            'type'              => 'ebook',
            'status'            => $status,
            'price'             => $this->price ?: 0,
            'views_count'       => 0,
            'buyers_count'      => 0,
            'revenue_total'     => 0,
            'cover_path'        => $coverPath,
            'primary_file_path' => $filePath,
            'primary_link_url'  => null,
            'ebook_chapters'    => $this->ebookChapters,
        ]);

        session()->flash(
            'status_contents',
            $status === 'pending_review'
                ? 'E-book berhasil dibuat dan sedang menunggu review admin Noorly.'
                : 'E-book berhasil dibuat dan sudah terbit.'
        );

        // cukup panggil redirect tanpa return
        redirect()->route('creator.contents.index');
    }

    public function render()
    {
        return view('livewire.creator.ebook-create');
    }
}
