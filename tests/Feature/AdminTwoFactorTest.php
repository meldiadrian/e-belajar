<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private GoogleAuthenticatorService $twoFactorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twoFactorService = new GoogleAuthenticatorService();
    }

    public function test_admin_can_view_2fa_section_in_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $response = $this->actingAs($admin)->get("/profile/{$admin->id}");

        $response->assertStatus(200);
        $response->assertSee('Autentikasi Dua Faktor (Google Authenticator)');
        $response->assertSee('2FA Belum Aktif');
        $response->assertSee('two_factor_code');
        $response->assertSee('two_factor_secret');
    }

    public function test_superadmin_can_view_2fa_section_in_profile(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $response = $this->actingAs($superadmin)->get("/profile/{$superadmin->id}");

        $response->assertStatus(200);
        $response->assertSee('Autentikasi Dua Faktor (Google Authenticator)');
        $response->assertSee('2FA Belum Aktif');
    }

    public function test_admin_cannot_enable_2fa_with_invalid_code(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $secret = $this->twoFactorService->generateSecretKey();

        $response = $this->actingAs($admin)->post("/profile/{$admin->id}/2fa/enable", [
            'two_factor_secret' => $secret,
            'two_factor_code' => '000000',
        ]);

        $response->assertSessionHasErrors('two_factor_code');
        $admin->refresh();
        $this->assertFalse($admin->hasTwoFactorEnabled());
        $this->assertNull($admin->two_factor_confirmed_at);
    }

    public function test_admin_can_enable_2fa_with_valid_code(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $secret = $this->twoFactorService->generateSecretKey();
        $timeSlice = (int) floor(time() / 30);
        $code = $this->twoFactorService->calculateCode($secret, $timeSlice);

        $response = $this->actingAs($admin)->post("/profile/{$admin->id}/2fa/enable", [
            'two_factor_secret' => $secret,
            'two_factor_code' => $code,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue($admin->hasTwoFactorEnabled());
        $this->assertEquals($secret, $admin->two_factor_secret);
        $this->assertNotNull($admin->two_factor_confirmed_at);
    }

    public function test_superadmin_can_enable_2fa_with_valid_code(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $secret = $this->twoFactorService->generateSecretKey();
        $timeSlice = (int) floor(time() / 30);
        $code = $this->twoFactorService->calculateCode($secret, $timeSlice);

        $response = $this->actingAs($superadmin)->post("/profile/{$superadmin->id}/2fa/enable", [
            'two_factor_secret' => $secret,
            'two_factor_code' => $code,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $superadmin->refresh();
        $this->assertTrue($superadmin->hasTwoFactorEnabled());
        $this->assertEquals($secret, $superadmin->two_factor_secret);
        $this->assertNotNull($superadmin->two_factor_confirmed_at);
    }

    public function test_admin_with_2fa_cannot_login_without_otp(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();

        $admin = User::factory()->create([
            'nip' => '198502022010011999',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'nip' => '198502022010011999',
            'password' => 'adminpassword',
            'two_factor_code' => '',
        ]);

        $response->assertSessionHasErrors('two_factor_code');
        $this->assertGuest();
    }

    public function test_admin_with_2fa_can_login_with_valid_otp(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();
        $timeSlice = (int) floor(time() / 30);
        $code = $this->twoFactorService->calculateCode($secret, $timeSlice);

        $admin = User::factory()->create([
            'nip' => '198502022010011888',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'nip' => '198502022010011888',
            'password' => 'adminpassword',
            'two_factor_code' => $code,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_disable_2fa_with_password(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();

        $admin = User::factory()->create([
            'password' => Hash::make('securepass123'),
            'role' => 'admin',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post("/profile/{$admin->id}/2fa/disable", [
            'current_password' => 'securepass123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertFalse($admin->hasTwoFactorEnabled());
        $this->assertNull($admin->two_factor_secret);
        $this->assertNull($admin->two_factor_confirmed_at);
    }

    public function test_superadmin_can_reset_2fa_for_another_admin(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $secret = $this->twoFactorService->generateSecretKey();
        $admin = User::factory()->create([
            'role' => 'admin',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->assertTrue($admin->hasTwoFactorEnabled());

        $response = $this->actingAs($superadmin)->post("/superadmin/users/{$admin->id}/reset-2fa");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertFalse($admin->hasTwoFactorEnabled());
        $this->assertNull($admin->two_factor_secret);
        $this->assertNull($admin->two_factor_confirmed_at);
    }
}
