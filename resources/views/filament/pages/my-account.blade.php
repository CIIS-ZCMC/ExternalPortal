<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-filament::button wire:click="updateProfile" icon="heroicon-o-check" size="lg">
                Save Profile Changes
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
