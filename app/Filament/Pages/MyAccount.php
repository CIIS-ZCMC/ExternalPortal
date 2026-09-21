<?php

namespace App\Filament\Pages;

use App\Models\ExternalEmployees;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyAccount extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.my-account';

    protected static ?string $title = 'My Profile & Account';

    protected static ?string $navigationLabel = 'My Account';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?int $navigationSort = 3;

    public $state = [
        'username' => '',
        'new_password' => '',
        'new_password_confirmation' => '',
        'current_password' => '',
        'first_name' => '',
        'last_name' => '',
        'middle_name' => '',
        'email' => '',
        'contact_number' => '',
        'address' => '',
        'agency' => '',
        'position' => '',
        'biometric_id' => '',
    ];

    public function mount()
    {
        $user = Auth::guard('external')->user();

        $this->state = [
            'username' => $user->username ?? '',
            'new_password' => '',
            'new_password_confirmation' => '',
            'current_password' => '',
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'middle_name' => $user->middle_name ?? '',
            'email' => $user->email ?? '',
            'contact_number' => $user->contact_number ?? '',
            'address' => $user->address ?? '',
            'agency' => $user->agency ?? '',
            'position' => $user->position ?? '',
            'biometric_id' => $user->biometric_id ?? '',
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Review your verified personnel identity, update contact details, and manage portal credentials.';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->icon(Heroicon::Check)
                ->color('primary')
                ->action(fn () => $this->updateProfile()),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Group::make([
                Section::make('Official Hospital Personnel Record')
                    ->description('Verified identity data synchronized with hospital administration.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextInput::make('biometric_id')
                            ->label('Biometric PIN')
                            ->disabled()
                            ->prefixIcon('heroicon-o-finger-print')
                            ->helperText('Official terminal PIN used for duty time tracking.')
                            ->columnSpan(1),

                        TextInput::make('agency')
                            ->label('Agency / Institution')
                            ->prefixIcon('heroicon-o-building-office')
                            ->columnSpan(1),

                        TextInput::make('position')
                            ->label('Official Designation / Position')
                            ->prefixIcon('heroicon-o-briefcase')
                            ->columnSpan(1),

                        TextInput::make('first_name')
                            ->label('First Name')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->disabled()
                            ->columnSpan(1),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                Section::make('Contact & Location Details')
                    ->description('Keep your active contact channels up to date for notifications.')
                    ->icon(Heroicon::OutlinedPhone)
                    ->schema([
                        TextInput::make('email')
                            ->label('Verified Official Email')
                            ->disabled()
                            ->prefixIcon('heroicon-o-envelope')
                            ->helperText('Used for account verification and official communication.')
                            ->columnSpan(1),

                        TextInput::make('contact_number')
                            ->label('Active Mobile Number')
                            ->prefixIcon('heroicon-o-phone')
                            ->placeholder('+63 900 000 0000')
                            ->columnSpan(2),

                        Textarea::make('address')
                            ->label('Residential / Mailing Address')
                            ->rows(2)
                            ->placeholder('Enter current residence address')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Portal Security & Credentials')
                    ->description('Update your portal password. Leave blank if you do not wish to change your password.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        TextInput::make('username')
                            ->label('Portal Username')
                            ->disabled()
                            ->prefixIcon('heroicon-o-user')
                            ->columnSpan(1),

                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->prefixIcon('heroicon-o-lock-closed')
                            ->placeholder('Required to set a new password')
                            ->columnSpan(1),

                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->prefixIcon('heroicon-o-key')
                            ->placeholder('Minimum 6 characters')
                            ->columnSpan(1),

                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->prefixIcon('heroicon-o-check-circle')
                            ->placeholder('Re-enter new password')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ])->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'state';
    }

    public function updateProfile()
    {
        $user = ExternalEmployees::where('id', Auth::guard('external')->user()->id);

        if (!empty($this->state['current_password']) || !empty($this->state['new_password'])) {
            if (empty($this->state['current_password'])) {
                Notification::make()
                    ->title('Current Password Required')
                    ->danger()
                    ->body('Please provide your current password to authorize a password change.')
                    ->send();
                return;
            }

            if (!Hash::check($this->state['current_password'], $user->first()->password)) {
                Notification::make()
                    ->title('Incorrect Current Password')
                    ->danger()
                    ->body('The current password entered is not valid.')
                    ->send();

                $this->state['current_password'] = '';
                $this->state['new_password'] = '';
                $this->state['new_password_confirmation'] = '';
                return;
            }

            if (empty($this->state['new_password']) || strlen($this->state['new_password']) < 6) {
                Notification::make()
                    ->title('Password Too Short')
                    ->danger()
                    ->body('Your new password must be at least 6 characters long.')
                    ->send();
                return;
            }

            if ($this->state['new_password'] !== $this->state['new_password_confirmation']) {
                Notification::make()
                    ->title('Password Confirmation Mismatch')
                    ->danger()
                    ->body('The new password confirmation does not match.')
                    ->send();
                return;
            }

            $user->update([
                'password' => Hash::make($this->state['new_password']),
            ]);

            $this->state['current_password'] = '';
            $this->state['new_password'] = '';
            $this->state['new_password_confirmation'] = '';
        }

        $user->update([
            'contact_number' => $this->state['contact_number'],
            'address' => $this->state['address'],
            'agency' => $this->state['agency'],
            'position' => $this->state['position'],
        ]);

        Notification::make()
            ->title('Profile Updated Successfully')
            ->success()
            ->body('Your profile details have been saved.')
            ->send();
    }
}
