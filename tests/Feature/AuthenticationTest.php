<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Portal');
        $response->assertSee('Nomor Induk Pegawai (NIP)');
        $response->assertSee('Kode Captcha');
        $response->assertSee('captcha-img');
    }

    public function test_user_can_login_with_valid_nip(): void
    {
        $user = User::factory()->create([
            'nip' => '198501012010011001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'nip' => '198501012010011001',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_nip_password(): void
    {
        User::factory()->create([
            'nip' => '198501012010011001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'nip' => '198501012010011001',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertGuest();
    }

    public function test_user_cannot_login_using_email_instead_of_nip(): void
    {
        User::factory()->create([
            'email' => 'user@bengkalis.go.id',
            'nip' => '198501012010011001',
            'password' => Hash::make('password123'),
        ]);

        // Percobaan login menggunakan email tanpa NIP harus ditolak validasi
        $responseWithoutNip = $this->post('/login', [
            'email' => 'user@bengkalis.go.id',
            'password' => 'password123',
        ]);

        $responseWithoutNip->assertSessionHasErrors('nip');
        $this->assertGuest();

        // Percobaan login memasukkan email pada input NIP tidak boleh berhasil
        $responseEmailAsNip = $this->post('/login', [
            'nip' => 'user@bengkalis.go.id',
            'password' => 'password123',
        ]);

        $responseEmailAsNip->assertSessionHasErrors('nip');
        $this->assertGuest();
    }

    public function test_user_can_register_new_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'email' => 'budi@bengkalis.go.id',
            'nip' => '199201012020011002',
            'nik' => '1403010101920002',
            'tempat_lahir' => 'Bengkalis',
            'agama' => 'Islam',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'institution' => 'Disdik Bengkalis',
            'phone' => '08123456789',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'budi@bengkalis.go.id',
            'nip' => '199201012020011002',
            'nik' => '1403010101920002',
            'tempat_lahir' => 'Bengkalis',
            'agama' => 'Islam',
            'role' => 'user',
        ]);
        $this->assertAuthenticated();
    }

    public function test_user_registered_with_nip_can_subsequently_login_with_nip(): void
    {
        $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@bengkalis.go.id',
            'nip' => '199503152021022005',
            'nik' => '1403055503950001',
            'tempat_lahir' => 'Mandau',
            'agama' => 'Islam',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->post('/logout');
        $this->assertGuest();

        $response = $this->post('/login', [
            'nip' => '199503152021022005',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
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
            'nip' => '198801012015011001',
            'password' => Hash::make('password123'),
        ]);

        $this->withSession(['login_captcha' => '54321'])
            ->post('/login', [
                'nip' => '198801012015011001',
                'password' => 'password123',
                'captcha' => '99999',
            ])
            ->assertSessionHasErrors('captcha');

        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_captcha(): void
    {
        $user = User::factory()->create([
            'nip' => '198801012015011001',
            'password' => Hash::make('password123'),
        ]);

        $this->withSession(['login_captcha' => '54321'])
            ->post('/login', [
                'nip' => '198801012015011001',
                'password' => 'password123',
                'captcha' => '54321',
            ])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_brute_force_more_than_ten_attempts_directs_to_404_page(): void
    {
        User::factory()->create([
            'nip' => '199001012020011005',
            'password' => Hash::make('secret123'),
        ]);

        // Lakukan 10 kali percobaan gagal pertama
        for ($i = 1; $i <= 10; $i++) {
            $response = $this->post('/login', [
                'nip' => '199001012020011005',
                'password' => 'wrong-pass-' . $i,
            ]);

            $response->assertSessionHasErrors('nip');
            $response->assertStatus(302);
        }

        // Percobaan ke-11 (lebih dari 10 kali): harus diarahkan ke 404
        $blockedResponse = $this->post('/login', [
            'nip' => '199001012020011005',
            'password' => 'wrong-pass-11',
        ]);

        $blockedResponse->assertStatus(404);
        $blockedResponse->assertSee('Halaman Tidak Ditemukan');
        $blockedResponse->assertSee('HTTP 404');
        $blockedResponse->assertSee('Kembali ke Beranda');

        // Setelah terblokir, akses GET ke login juga mengembalikan 404
        $loginPageResponse = $this->get('/login');
        $loginPageResponse->assertStatus(404);
        $loginPageResponse->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_successful_login_clears_failed_attempt_counter(): void
    {
        $user = User::factory()->create([
            'nip' => '199102022020021006',
            'password' => Hash::make('secret123'),
        ]);

        // Coba gagal 3 kali
        for ($i = 1; $i <= 3; $i++) {
            $this->post('/login', [
                'nip' => '199102022020021006',
                'password' => 'wrong-pass',
            ])->assertSessionHasErrors('nip');
        }

        // Login berhasil pada percobaan berikutnya
        $successResponse = $this->post('/login', [
            'nip' => '199102022020021006',
            'password' => 'secret123',
        ]);

        $successResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Setelah logout, akses GET /login tetap normal (tidak 404)
        $this->post('/logout');
        $this->get('/login')->assertStatus(200);
    }
}

