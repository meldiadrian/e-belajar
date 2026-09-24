<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Portal');
        $response->assertSee('Kode Captcha');
        $response->assertSee('captcha-img');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@bengkalis.go.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@bengkalis.go.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'user@bengkalis.go.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@bengkalis.go.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_register_new_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'email' => 'budi@bengkalis.go.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'institution' => 'Disdik Bengkalis',
            'phone' => '08123456789',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'budi@bengkalis.go.id',
            'role' => 'user',
        ]);
        $this->assertAuthenticated();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_captcha_endpoint_returns_png_and_stores_session(): void
    {
        $response = $this->get('/captcha');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertNotEmpty(session('login_captcha'));
        $this->assertEquals(5, strlen((string) session('login_captcha')));
    }

    public function test_user_cannot_login_with_invalid_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'user@bengkalis.go.id',
            'password' => Hash::make('password123'),
        ]);

        $this->withSession(['login_captcha' => '54321'])
            ->post('/login', [
                'email' => 'user@bengkalis.go.id',
                'password' => 'password123',
                'captcha' => '99999',
            ])
            ->assertSessionHasErrors('captcha');

        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'user@bengkalis.go.id',
            'password' => Hash::make('password123'),
        ]);

        $this->withSession(['login_captcha' => '54321'])
            ->post('/login', [
                'email' => 'user@bengkalis.go.id',
                'password' => 'password123',
                'captcha' => '54321',
            ])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }
}
