<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-filament::button wire:click="updateProfile" icon="heroicon-o-check" size="lg">
                Save Profile Changes
            </x-filament::button>
        </div>

        {{-- Google Authenticator (TOTP 2FA) Setup & Management --}}
        <div class="gauth-section-wrapper" style="margin-top: 2.25rem; padding-top: 1.75rem; border-top: 1px solid #e2e8f0;">
            @include('settings.two-factor-setup')
        </div>
    </div>
</x-filament-panels::page>
