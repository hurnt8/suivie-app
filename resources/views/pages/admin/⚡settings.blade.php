<?php

use App\Models\Settings;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Paramètres')] class extends Component {
    use WithFileUploads;

    public string $company_name = '';
    public ?string $address = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $domain = null;
    public string $tracking_prefix = '';
    public string $currency = 'EUR';
    public string $default_locale = 'fr';
    public ?string $mail_from_address = null;
    public ?string $mail_from_name = null;
    public ?string $smtp_host = null;
    public ?int $smtp_port = null;
    public ?string $smtp_username = null;
    public ?string $smtp_password = null;
    public ?string $smtp_encryption = null;

    public $logo = null;
    public ?string $currentLogoUrl = null;

    public function mount(): void
    {
        $settings = Settings::current();

        $this->company_name = $settings->company_name;
        $this->address = $settings->address;
        $this->phone = $settings->phone;
        $this->email = $settings->email;
        $this->domain = $settings->domain;
        $this->tracking_prefix = $settings->tracking_prefix;
        $this->currency = $settings->currency;
        $this->default_locale = $settings->default_locale;
        $this->mail_from_address = $settings->mail_from_address;
        $this->mail_from_name = $settings->mail_from_name;
        $this->smtp_host = $settings->smtp_host;
        $this->smtp_port = $settings->smtp_port;
        $this->smtp_username = $settings->smtp_username;
        $this->currentLogoUrl = $settings->logo_path ? Storage::disk('public')->url($settings->logo_path) : null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'tracking_prefix' => ['required', 'string', 'max:10', 'alpha'],
            'currency' => ['required', 'string', 'size:3'],
            'default_locale' => ['required', 'string', 'in:fr,en,es,de,it,pt,ro,pl'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $settings = Settings::current();

        if ($this->logo) {
            $validated['logo_path'] = $this->logo->store('logos', 'public');
        }

        if (blank($validated['smtp_password'] ?? null)) {
            unset($validated['smtp_password']);
        }

        unset($validated['logo']);

        $settings->update($validated);

        Flux::toast(variant: 'success', text: __('admin.settings_saved_toast'));

        $this->currentLogoUrl = $settings->logo_path ? Storage::disk('public')->url($settings->logo_path) : $this->currentLogoUrl;
        $this->smtp_password = null;
    }
}; ?>

<div class="max-w-4xl space-y-6">
    <div>
        <flux:heading size="xl">{{ __('admin.settings_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.settings_subtitle') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="card-elegant p-6">
            <flux:heading size="lg" class="mb-5">{{ __('admin.settings_section_brand') }}</flux:heading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="company_name" :label="__('admin.field_company')" class="sm:col-span-2" />

                <div class="sm:col-span-2">
                    <flux:input type="file" wire:model="logo" :label="__('admin.field_logo')" accept="image/*" />
                    @if ($currentLogoUrl)
                        <img src="{{ $currentLogoUrl }}" alt="Logo" class="mt-3 h-12 rounded border border-zinc-200 object-contain dark:border-zinc-700">
                    @endif
                </div>

                <flux:input wire:model="address" :label="__('admin.field_address')" class="sm:col-span-2" />
                <flux:input wire:model="phone" :label="__('admin.field_phone')" />
                <flux:input wire:model="email" type="email" :label="__('admin.field_email')" />
                <flux:input wire:model="domain" :label="__('admin.field_domain')" placeholder="livrion.example" />
            </div>
        </div>

        <div class="card-elegant p-6">
            <flux:heading size="lg" class="mb-5">{{ __('admin.settings_section_tracking') }}</flux:heading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <flux:input wire:model="tracking_prefix" :label="__('admin.field_tracking_prefix')" />
                <flux:input wire:model="currency" :label="__('admin.field_currency')" maxlength="3" />
                <flux:select wire:model="default_locale" :label="__('admin.field_default_locale')">
                    @foreach (\App\Support\Locales::NAMES as $code => $name)
                        <flux:select.option :value="$code">{{ $name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="card-elegant p-6">
            <flux:heading size="lg" class="mb-1">{{ __('admin.settings_section_mail') }}</flux:heading>
            <flux:subheading class="mb-5">{{ __('admin.settings_mail_hint') }}</flux:subheading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="mail_from_address" type="email" :label="__('admin.field_mail_from_address')" />
                <flux:input wire:model="mail_from_name" :label="__('admin.field_mail_from_name')" />

                <flux:input wire:model="smtp_host" :label="__('admin.field_smtp_host')" />
                <flux:input wire:model="smtp_port" type="number" :label="__('admin.field_smtp_port')" />
                <flux:input wire:model="smtp_username" :label="__('admin.field_smtp_username')" />
                <flux:input wire:model="smtp_password" type="password" :label="__('admin.field_smtp_password')" :description="__('admin.password_leave_blank')" />

                <flux:select wire:model="smtp_encryption" :label="__('admin.field_smtp_encryption')">
                    <flux:select.option value="">{{ __('admin.smtp_encryption_none') }}</flux:select.option>
                    <flux:select.option value="tls">TLS</flux:select.option>
                    <flux:select.option value="ssl">SSL</flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('admin.save') }}</flux:button>
        </div>
    </form>
</div>
