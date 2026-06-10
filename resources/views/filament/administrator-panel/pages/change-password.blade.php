<x-filament-panels::page>
    <div style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Current Password</label>
                <input type="password" wire:model="current_password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                @error('current_password')
                    <p style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">New Password</label>
                <input type="password" wire:model="new_password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <p style="color: #666; font-size: 14px; margin-top: 5px;">Minimum 5 characters</p>
                @error('new_password')
                    <p style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Confirm New Password</label>
                <input type="password" wire:model="new_password_confirmation" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                @error('new_password_confirmation')
                    <p style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="button" wire:click="changePassword" style="padding: 10px 20px; background: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">
                    Change Password
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
