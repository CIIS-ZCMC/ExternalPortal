<x-filament-panels::page>
    <div class="max-w-2xl mx-auto w-full">
        <form wire:submit.prevent="changePassword" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-filament::button type="submit" icon="heroicon-o-lock-closed" size="lg">
                    Update Password
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
