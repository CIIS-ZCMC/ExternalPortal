<?php

namespace App\Filament\AdministratorPanel\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.administrator-panel.pages.change-password';

    protected static ?string $title = 'Change Password';

    protected static ?string $navigationLabel = 'Change Password';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static ?int $navigationSort = 4;

    public static bool $shouldRegisterNavigation = true;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Update your administrative account password to maintain security.';
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Security Credentials')
                ->description('Ensure your account is protected with a strong, unique password.')
                ->icon(Heroicon::OutlinedKey)
                ->schema([
                    TextInput::make('current_password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->label('Current Password')
                        ->placeholder('Enter your current administrator password')
                        ->prefixIcon('heroicon-o-lock-closed'),

                    TextInput::make('new_password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(6)
                        ->same('new_password_confirmation')
                        ->label('New Password')
                        ->placeholder('Enter a strong new password')
                        ->helperText('Must be at least 6 characters long.')
                        ->prefixIcon('heroicon-o-key'),

                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->revealable()
                        ->required()
                        ->label('Confirm New Password')
                        ->placeholder('Re-type the new password')
                        ->prefixIcon('heroicon-o-check-circle'),
                ])
                ->columns(1),
        ];
    }

    public function changePassword(): void
    {
        $data = $this->form->getState();

        $user = auth('administrator')->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => 'The current password provided is incorrect.',
            ]);
        }

        DB::connection('external_employees')
            ->table('administrators')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($data['new_password']),
            ]);

        Notification::make()
            ->title('Password Updated Successfully')
            ->body('Your administrator password has been changed.')
            ->success()
            ->send();

        $this->form->fill();
    }
}
