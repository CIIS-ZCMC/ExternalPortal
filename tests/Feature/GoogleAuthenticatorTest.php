<?php

namespace Tests\Feature;

use App\Models\ExternalEmployees;
use App\Services\GAuthClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GoogleAuthenticatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('external_employees')) {
            Schema::create('external_employees', function ($table) {
                $table->id();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('middle_name')->nullable();
                $table->string('ext_name')->nullable();
                $table->string('email')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('contact_number')->nullable();
                $table->text('address')->nullable();
                $table->string('agency')->nullable();
                $table->string('position')->nullable();
                $table->string('status')->nullable();
                $table->integer('biometric_id')->nullable();
                $table->boolean('is_2fa_enabled')->default(false);
                $table->timestamp('two_factor_confirmed_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function test_2fa_challenge_redirects_to_login_if_no_pending_session()
    {
        $response = $this->get(route('2fa.challenge'));
        $response->assertRedirect(route('portal.login'));
    }

    public function test_login_proceeds_directly_when_2fa_is_disabled()
    {
        $employee = ExternalEmployees::create([
            'username' => 'test_user_direct_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'direct_' . time() . '@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_number' => '09123456789',
            'address' => 'Zamboanga City',
            'biometric_id' => 99910,
            'email_verified_at' => now(),
            'is_2fa_enabled' => false,
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => $employee->username,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/portal');
        $this->assertAuthenticatedAs($employee, 'external');

        $employee->forceDelete();
    }

    public function test_login_intercepts_and_redirects_to_2fa_challenge_when_2fa_is_enabled()
    {
        $employee = ExternalEmployees::create([
            'username' => 'test_user_2fa_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'twofa_' . time() . '@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'contact_number' => '09123456780',
            'address' => 'Zamboanga City',
            'biometric_id' => 99911,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => $employee->username,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('2fa.challenge'));
        $response->assertSessionHas('auth.2fa.user_id', $employee->id);
        $response->assertSessionHas('auth.2fa.guard', 'external');

        // Not authenticated yet
        $this->assertGuest('external');

        $employee->forceDelete();
    }

    public function test_two_factor_challenge_page_renders_with_pending_session()
    {
        $employee = ExternalEmployees::create([
            'username' => 'test_render_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'render_' . time() . '@example.com',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'contact_number' => '09123456781',
            'address' => 'Zamboanga City',
            'biometric_id' => 99912,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
        ]);

        $response = $this->withSession([
            'auth.2fa.user_id' => $employee->id,
            'auth.2fa.guard' => 'external',
        ])->get(route('2fa.challenge'));

        $response->assertStatus(200);
        $response->assertSee('Two-Step Verification');
        $response->assertSee('Maria Santos');
        $response->assertSee('challenge-form');

        $employee->forceDelete();
    }

    public function test_verify_challenge_fails_with_invalid_code()
    {
        Http::fake([
            '*/api/v1/2fa/verify' => Http::response([
                'verified' => false,
                'status' => 400,
                'reason' => 'invalid_code',
                'remaining_attempts' => 4,
                'is_locked' => false,
                'message' => 'Invalid code entered.',
            ], 400),
        ]);

        $employee = ExternalEmployees::create([
            'username' => 'test_invalid_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'invalid_' . time() . '@example.com',
            'first_name' => 'Carlos',
            'last_name' => 'Reyes',
            'contact_number' => '09123456782',
            'address' => 'Zamboanga City',
            'biometric_id' => 99913,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
        ]);

        $response = $this->withSession([
            'auth.2fa.user_id' => $employee->id,
            'auth.2fa.guard' => 'external',
        ])->post(route('2fa.verify'), [
            'code' => '000000',
            'is_recovery_code' => 0,
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest('external');

        $employee->forceDelete();
    }

    public function test_verify_challenge_succeeds_with_valid_code()
    {
        Http::fake([
            '*/api/v1/2fa/verify' => Http::response([
                'verified' => true,
                'method' => 'totp',
            ], 200),
        ]);

        $employee = ExternalEmployees::create([
            'username' => 'test_valid_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'valid_' . time() . '@example.com',
            'first_name' => 'Pedro',
            'last_name' => 'Penduko',
            'contact_number' => '09123456783',
            'address' => 'Zamboanga City',
            'biometric_id' => 99914,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
        ]);

        $response = $this->withSession([
            'auth.2fa.user_id' => $employee->id,
            'auth.2fa.guard' => 'external',
        ])->post(route('2fa.verify'), [
            'code' => '123456',
            'is_recovery_code' => 0,
        ]);

        $response->assertRedirect('/portal');
        $this->assertAuthenticatedAs($employee, 'external');
        $this->assertNull(session('auth.2fa.user_id'));

        $employee->forceDelete();
    }

    public function test_settings_enroll_and_activate_endpoints()
    {
        Http::fake([
            '*/api/v1/2fa/enroll' => Http::response([
                'enrollment_id' => 'test-enrollment-id',
                'status' => 'pending',
                'qr_code_data_uri' => 'data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=',
                'manual_entry_key' => 'JBSWY3DPEHPK3PXP',
                'recovery_codes' => ['1111-2222', '3333-4444'],
            ], 201),
            '*/api/v1/2fa/activate' => Http::response([
                'success' => true,
                'message' => '2FA Activated',
            ], 200),
            '*/api/v1/2fa/reset' => Http::response([
                'success' => true,
                'message' => '2FA reset successfully.',
            ], 200),
        ]);

        $employee = ExternalEmployees::create([
            'username' => 'test_settings_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'settings_' . time() . '@example.com',
            'first_name' => 'Ana',
            'last_name' => 'Lim',
            'contact_number' => '09123456784',
            'address' => 'Zamboanga City',
            'biometric_id' => 99915,
            'email_verified_at' => now(),
            'is_2fa_enabled' => false,
        ]);

        // 1. Enroll
        $enrollResponse = $this->actingAs($employee, 'external')
            ->postJson(route('settings.2fa.enroll'));
        $enrollResponse->assertStatus(200);
        $enrollResponse->assertJsonPath('success', true);
        $enrollResponse->assertJsonPath('data.manual_entry_key', 'JBSWY3DPEHPK3PXP');

        // 2. Activate
        $activateResponse = $this->actingAs($employee, 'external')
            ->postJson(route('settings.2fa.activate'), ['code' => '123456']);
        $activateResponse->assertStatus(200);
        $activateResponse->assertJsonPath('success', true);

        $employee->refresh();
        $this->assertTrue((bool) $employee->is_2fa_enabled);
        $this->assertNotNull($employee->two_factor_confirmed_at);

        // 3. Status
        $statusResponse = $this->actingAs($employee, 'external')
            ->getJson(route('settings.2fa.status'));
        $statusResponse->assertStatus(200);
        $statusResponse->assertJsonPath('is_2fa_enabled', true);

        // 4. Disable
        $disableResponse = $this->actingAs($employee, 'external')
            ->postJson(route('settings.2fa.disable'));
        $disableResponse->assertStatus(200);

        $employee->refresh();
        $this->assertFalse((bool) $employee->is_2fa_enabled);
        $this->assertNull($employee->two_factor_confirmed_at);

        $employee->forceDelete();
    }

    public function test_verify_challenge_handles_temporary_lockout()
    {
        Http::fake([
            '*/api/v1/2fa/verify' => Http::response([
                'verified' => false,
                'status' => 423,
                'reason' => 'account_locked',
                'is_locked' => true,
                'locked_until' => '2026-10-26 15:30:00',
                'message' => 'Account is temporarily locked due to 5 failed attempts.',
            ], 423),
        ]);

        $employee = ExternalEmployees::create([
            'username' => 'test_locked_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'locked_' . time() . '@example.com',
            'first_name' => 'Locke',
            'last_name' => 'Cole',
            'contact_number' => '09123456785',
            'address' => 'Zamboanga City',
            'biometric_id' => 99916,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
        ]);

        $response = $this->withSession([
            'auth.2fa.user_id' => $employee->id,
            'auth.2fa.guard' => 'external',
        ])->post(route('2fa.verify'), [
            'code' => '999999',
            'is_recovery_code' => 0,
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest('external');

        $employee->forceDelete();
    }

    public function test_verify_challenge_succeeds_with_recovery_code()
    {
        Http::fake([
            '*/api/v1/2fa/verify' => Http::response([
                'verified' => true,
                'method' => 'recovery_code',
                'remaining_recovery_codes' => 7,
            ], 200),
        ]);

        $employee = ExternalEmployees::create([
            'username' => 'test_recovery_' . time(),
            'password' => Hash::make('password123'),
            'email' => 'recovery_' . time() . '@example.com',
            'first_name' => 'Terra',
            'last_name' => 'Branford',
            'contact_number' => '09123456786',
            'address' => 'Zamboanga City',
            'biometric_id' => 99917,
            'email_verified_at' => now(),
            'is_2fa_enabled' => true,
        ]);

        $response = $this->withSession([
            'auth.2fa.user_id' => $employee->id,
            'auth.2fa.guard' => 'external',
        ])->post(route('2fa.verify'), [
            'code' => 'ABCD-1234',
            'is_recovery_code' => 1,
        ]);

        $response->assertRedirect('/portal');
        $this->assertAuthenticatedAs($employee, 'external');
        $this->assertNull(session('auth.2fa.user_id'));

        $employee->forceDelete();
    }
}
