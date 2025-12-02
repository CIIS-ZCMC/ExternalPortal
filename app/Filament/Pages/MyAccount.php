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

    protected static ?string $title = 'My Account';

    protected static ?string $navigationLabel = 'My Account';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;


    public $state = [
        'username' => "",
        'new_password' => "",
        'current_password' => "",
        'first_name' => "",
        'last_name' => "",
        'middle_name' => "",
        'email' => "",
        'contact_number' => "",
        'address' => "",
        'agency' => "",
        'position' => "",
        'biometric_id' => "",
    ];

    public function mount()
    {

        $this->state = [
            'username' => Auth::guard('external')->user()->username,
            'new_password' => "",
            'current_password' => "",
            'first_name' => Auth::guard('external')->user()->first_name,
            'last_name' => Auth::guard('external')->user()->last_name,
            'middle_name' => Auth::guard('external')->user()->middle_name,
            'email' => Auth::guard('external')->user()->email,
            'contact_number' => Auth::guard('external')->user()->contact_number,
            'address' => Auth::guard('external')->user()->address,
            'agency' => Auth::guard('external')->user()->agency,
            'position' => Auth::guard('external')->user()->position,
            'biometric_id' => Auth::guard('external')->user()->biometric_id,
        ];
    }


    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage your account';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('Update Profile')
                ->icon(Heroicon::UserCircle)
                ->action(function () {
                    $this->updateProfile();
                }),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Group::make([
                Section::make("Personal Information")
                    ->description('Manage Personal Information')
                    ->schema([
                        TextInput::make('biometric_id')
                            ->label("Biometric ID")
                            ->required()
                            ->disabled()
                            ->columnSpan(3)
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        TextInput::make('middle_name')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->disabled()
                            ->columnSpan(2)
                            ->maxLength(255),
                        TextInput::make('contact_number')

                            ->maxLength(255),
                        Textarea::make('address')

                            ->columnSpan(3)
                            ->maxLength(255),
                        TextInput::make('agency')

                            ->maxLength(255),
                        TextInput::make('position')

                            ->maxLength(255),

                    ])
                    ->columns(3),
                Section::make("Account Information")
                    ->description('Manage Account Information')
                    ->schema([

                        TextInput::make('username')

                            ->disabled()
                            ->maxLength(255),
                        TextInput::make('current_password')
                            ->password()
                            ->maxLength(255),
                        TextInput::make('new_password')
                            ->password()
                            ->maxLength(255),


                    ])
                    ->columns(3),



            ])->columnSpanFull()
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'state';
    }

    public function updateProfile()
    {
        $user = ExternalEmployees::where('id', Auth::guard('external')->user()->id);
        if (!empty($this->state['current_password']) && !empty($this->state['new_password'])) {

            if (Hash::check($this->state['current_password'], $user->first()->password)) {
                $user->update([
                    'password' => Hash::make($this->state['new_password'])
                ]);
            } else {
                Notification::make()
                    ->title('Invalid Password')
                    ->danger()
                    ->body('Your current password is incorrect.')
                    ->send();

                $this->state['current_password'] = "";
                $this->state['new_password'] = "";
                return;
            }
        }
        $user->update([
            'contact_number' => $this->state['contact_number'],
            'address' => $this->state['address'],
            'agency' => $this->state['agency'],
            'position' => $this->state['position'],
        ]);

        Notification::make()
            ->title('Profile Updated')
            ->success()
            ->body('Your profile has been updated successfully.')
            ->send();
        $this->state['current_password'] = "";
        $this->state['new_password'] = "";
    }
}
