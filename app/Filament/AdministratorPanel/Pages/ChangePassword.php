<?php

namespace App\Filament\AdministratorPanel\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePassword extends Page
{
    protected string $view = 'filament.administrator-panel.pages.change-password';

    protected static ?string $title = 'Change Password';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?int $navigationSort = 4;

    public static bool $shouldRegisterNavigation = true;

    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('current_password')
                ->password()
                ->required()
                ->label('Current Password'),
            TextInput::make('new_password')
                ->password()
                ->required()
                ->minLength(8)
                ->label('New Password')
                ->same('new_password_confirmation'),
            TextInput::make('new_password_confirmation')
                ->password()
                ->required()
                ->label('Confirm New Password'),
        ];
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:5|same:new_password_confirmation',
            'new_password_confirmation' => 'required',
        ]);

        $user = auth('administrator')->user();

        if (!Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        DB::connection('external_employees')
            ->table('administrators')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($this->new_password),
            ]);

        Notification::make()
            ->title('Password Changed')
            ->body('Your password has been changed successfully.')
            ->success()
            ->send();

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }
}
