<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityFirewallTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_requests_pass_without_block(): void
    {
        $response = $this->get('/?search=multimedia');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString("frame-ancestors 'self'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_sql_injection_union_select_is_blocked(): void
    {
        $response = $this->get('/?search=1%20UNION%20SELECT%201,2,3--');
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Permintaan tidak valid: Muatan berbahaya terdeteksi.']);
    }

    public function test_sql_injection_boolean_tautology_is_blocked(): void
    {
        $response = $this->get('/?search=\'%20OR%20\'1\'=\'1');
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Permintaan tidak valid: Muatan berbahaya terdeteksi.']);
    }

    public function test_sql_injection_sleep_benchmark_is_blocked(): void
    {
        $response = $this->get('/?id=1%20AND%20SLEEP(5)');
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Permintaan tidak valid: Muatan berbahaya terdeteksi.']);
    }

    public function test_scanning_tool_user_agent_sqlmap_is_blocked(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'sqlmap/1.6.12#stable (https://sqlmap.org)',
        ])->get('/');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Akses ditolak: Alat pemindai keamanan otomatis terdeteksi.']);
    }

    public function test_scanning_tool_user_agent_nikto_is_blocked(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.00 (Nikto/2.1.6) (Evasions:None) (Test:Port Check)',
        ])->get('/');

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Akses ditolak: Alat pemindai keamanan otomatis terdeteksi.']);
    }

    public function test_scanning_tool_sensitive_file_probe_is_blocked(): void
    {
        $response = $this->get('/.env');
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Halaman atau berkas tidak ditemukan.']);

        $gitResponse = $this->get('/.git/config');
        $gitResponse->assertStatus(404);
    }

    public function test_executable_php_file_upload_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'User Test',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unggahan berkas tidak diizinkan: Berkas yang dapat menjalankan skrip dilarang.']);
    }

    public function test_double_extension_script_upload_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('image.php.png', 10, 'image/png');

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'User Test',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unggahan berkas tidak diizinkan: Berkas yang dapat menjalankan skrip dilarang.']);
    }

    public function test_shell_script_upload_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('exploit.sh', 10, 'text/x-shellscript');

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'User Test',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unggahan berkas tidak diizinkan: Berkas yang dapat menjalankan skrip dilarang.']);
    }

    public function test_file_containing_php_code_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->createWithContent('photo.png', "<?php phpinfo(); ?>");

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'User Test',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unggahan berkas tidak diizinkan: Berkas yang dapat menjalankan skrip dilarang.']);
    }

    public function test_valid_image_upload_passes_firewall(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->image('profile.jpg', 100, 100);

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'Valid User',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        // Request passes firewall and profile is updated successfully
        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }
}
