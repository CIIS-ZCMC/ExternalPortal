<?php

namespace App\Filament\Admin\Pages;

use App\Livewire\UsersListsWidget;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class UsersLists extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.admin.pages.users-lists';

    public function getHeaderActions(): array
    {
        return [
            Action::make("Logout")
                ->label("Logout")
                ->color("danger")
                ->icon("heroicon-o-arrow-right-on-rectangle")
                ->action(function () {
                    session()->forget("admin_user");
                    return redirect()->route("admin.login");
                }),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            UsersListsWidget::class,
        ];
    }
}
