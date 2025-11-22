<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use App\Models\PlatformSetting;

class SettingsIndex extends Component
{
    public bool $sidebarOpen = true;
    public bool $hasSettingsTable = false;

    public string $platform_name    = '';
    public string $platform_tagline = '';
    public string $platform_domain  = '';
    public string $support_email    = '';

    public ?int $platform_fee_percent = 10;
    public ?int $min_withdraw_amount  = 50000;
    public bool $maintenance_mode     = false;

    protected $rules = [
        'platform_name'         => 'required|string|max:255',
        'platform_tagline'      => 'nullable|string|max:255',
        'platform_domain'       => 'nullable|string|max:255',
        'support_email'         => 'nullable|email|max:255',
        'platform_fee_percent'  => 'nullable|integer|min:0|max:100',
        'min_withdraw_amount'   => 'nullable|integer|min:0',
        'maintenance_mode'      => 'boolean',
    ];

    /**
     * Helper untuk load ulang nilai dari DB ke property.
     */
    protected function reloadFromDb(): void
    {
        if (! $this->hasSettingsTable) {
            return;
        }

        $this->platform_name        = $this->getSetting('platform_name', 'Noorly Studio');
        $this->platform_tagline     = $this->getSetting('platform_tagline', 'Bantu kreator menjual produk digital.');
        $this->platform_domain      = $this->getSetting('platform_domain', config('app.url'));
        $this->support_email        = $this->getSetting('support_email', 'support@example.com');
        $this->platform_fee_percent = (int) $this->getSetting('platform_fee_percent', 10);
        $this->min_withdraw_amount  = (int) $this->getSetting('min_withdraw_amount', 50000);
        $this->maintenance_mode     = (bool) $this->getSetting('maintenance_mode', false);
    }

    public function mount(): void
    {
        // cek tabel sudah ada atau belum
        $this->hasSettingsTable = Schema::hasTable('platform_settings');

        if (! $this->hasSettingsTable) {
            return;
        }

        // load awal dari DB
        $this->reloadFromDb();
    }

    /**
     * Helper ambil setting dari DB.
     */
    protected function getSetting(string $key, $default = null)
    {
        $row = PlatformSetting::where('key', $key)->first();

        return $row ? $row->value : $default;
    }

    /**
     * Helper simpan setting ke DB.
     */
    protected function setSetting(string $key, $value): void
    {
        PlatformSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) || is_int($value)
                    ? (string) $value
                    : $value,
            ]
        );
    }

    /**
     * Simpan perubahan pengaturan.
     */
    public function save(): void
    {
        if (! $this->hasSettingsTable) {
            session()->flash('status_settings', 'Tabel pengaturan platform belum dibuat. Jalankan migrasi dulu.');
            return;
        }

        $this->validate();

        $this->setSetting('platform_name', $this->platform_name);
        $this->setSetting('platform_tagline', $this->platform_tagline);
        $this->setSetting('platform_domain', $this->platform_domain);
        $this->setSetting('support_email', $this->support_email);
        $this->setSetting('platform_fee_percent', (int) $this->platform_fee_percent);
        $this->setSetting('min_withdraw_amount', (int) $this->min_withdraw_amount);
        $this->setSetting('maintenance_mode', $this->maintenance_mode ? '1' : '0');

        session()->flash('status_settings', 'Pengaturan platform berhasil disimpan.');
    }

    /**
     * Reset form ke nilai terakhir dari database.
     */
    public function resetForm(): void
    {
        if (! $this->hasSettingsTable) {
            return;
        }

        $this->reloadFromDb();
        $this->resetValidation();

        session()->flash('status_settings', 'Perubahan dibatalkan. Pengaturan dikembalikan ke nilai terakhir.');
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function render()
    {
        return view('livewire.admin.settings-index', [
            'hasSettingsTable' => $this->hasSettingsTable,
        ]);
    }
}
