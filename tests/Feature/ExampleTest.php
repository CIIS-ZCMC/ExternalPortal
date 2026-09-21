<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('portal.login'));

        $loginResponse = $this->get(route('portal.login'));
        $loginResponse->assertStatus(200);
    }

    public function test_administrators_table_columns(): void
    {
        $admin = new \App\Models\Administrator([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 1,
        ]);

        $table = \App\Filament\AdministratorPanel\Resources\Administrators\Tables\AdministratorsTable::configure(
            \Filament\Tables\Table::make(\Livewire\Livewire::new(\App\Filament\AdministratorPanel\Resources\Administrators\Pages\ListAdministrators::class))
        );

        $roleColumn = $table->getColumn('role');
        $this->assertNotNull($roleColumn);
        $roleColumn->record($admin);
        $this->assertEquals('Super Administrator', $roleColumn->formatState($admin->role));
    }

    public function test_schedules_table_columns(): void
    {
        $schedule = new \App\Models\CustomSchedule([
            'is_shifting' => true,
        ]);

        $table = \App\Filament\Resources\Schedules\Tables\SchedulesTable::configure(
            \Filament\Tables\Table::make(\Livewire\Livewire::new(\App\Filament\Resources\Schedules\Pages\ListSchedules::class))
        );

        $shiftColumn = $table->getColumn('is_shifting');
        $this->assertNotNull($shiftColumn);
        $shiftColumn->record($schedule);
        $this->assertEquals('Shifting', $shiftColumn->formatState($schedule->is_shifting));
    }

    public function test_devices_table_columns(): void
    {
        $device = new \App\Models\Devices([
            'device_name' => 'Main Gate Scanner',
            'ip_address' => '192.168.1.100',
            'for_attendance' => 1,
            'is_registration' => 0,
            'is_active' => 1,
        ]);

        $table = \App\Filament\AdministratorPanel\Resources\Devices\Tables\DevicesTable::configure(
            \Filament\Tables\Table::make(\Livewire\Livewire::new(\App\Filament\AdministratorPanel\Resources\Devices\Pages\ListDevices::class))
        );

        $roleColumn = $table->getColumn('for_attendance');
        $this->assertNotNull($roleColumn);
        $roleColumn->record($device);
        $this->assertEquals('Attendance Terminal', $roleColumn->formatState($device->for_attendance));
    }

    public function test_dtr_view_handles_offline_api_gracefully(): void
    {
        $user = new \App\Models\ExternalEmployees([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'biometric_id' => '8008',
            'email' => 'john@example.com',
        ]);
        $this->actingAs($user, 'external');

        \Illuminate\Support\Facades\Http::fake([
            '*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('cURL error 7: Failed to connect to host');
            },
        ]);

        $dtrView = new \App\Livewire\DTRView();
        $dtrView->year = 2026;
        $dtrView->month = 9;

        $records = $dtrView->getDtrRecords();
        $this->assertTrue($records->isEmpty());

        $dtrView->refreshDtr();
        $this->assertTrue(true);
    }

    public function test_apply_filter_on_components(): void
    {
        $user = new \App\Models\ExternalEmployees([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'biometric_id' => '8008',
            'email' => 'john@example.com',
        ]);
        $this->actingAs($user, 'external');

        \Livewire\Livewire::test(\App\Livewire\FilterWidget::class)
            ->set('selectedMonth', 11)
            ->set('selectedYear', 2025)
            ->assertDispatched('applyFilter');

        \Livewire\Livewire::test(\App\Filament\Resources\Schedules\Pages\ListSchedules::class)
            ->dispatch('applyFilter', 11, 2025)
            ->assertSet('month', 11)
            ->assertSet('year', 2025);

        \Livewire\Livewire::test(\App\Livewire\DTRView::class)
            ->dispatch('applyFilter', 11, 2025)
            ->assertSet('month', 11)
            ->assertSet('year', 2025);
    }

    public function test_portal_pages_render_successfully(): void
    {
        $user = new \App\Models\ExternalEmployees([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'biometric_id' => '8008',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $user->id = 1;
        $user->exists = true;
        $this->actingAs($user, 'external');

        $dtrResponse = $this->get('/portal/d-t-r');
        $dtrResponse->assertStatus(200);

        $schedulesResponse = $this->get('/portal/schedules');
        $schedulesResponse->assertStatus(200);
    }
}
