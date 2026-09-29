<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\CertificateSetting;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_certificate_settings_crud(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.certificates.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_certificate_settings_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $signer = CertificateSetting::create([
            'name' => 'AGUS SOFYAN, S.STP.,MPA',
            'nip' => '197908161998021001',
            'jabatan' => 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => 'Kabupaten Bengkalis',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.certificates.index'));
        $response->assertStatus(200);
        $response->assertSee('AGUS SOFYAN, S.STP.,MPA');
        $response->assertSee('197908161998021001');
    }

    public function test_admin_can_create_new_certificate_signer_with_image_upload(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $image = UploadedFile::fake()->image('signature.png', 400, 200);

        $response = $this->actingAs($admin)->post(route('admin.certificates.store'), [
            'name' => 'Dr. H. Ahmad Fauzi, M.Si',
            'nip' => '198001012005011002',
            'jabatan' => 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => 'Kabupaten Bengkalis',
            'signature_image' => $image,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.certificates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('certificate_settings', [
            'name' => 'Dr. H. Ahmad Fauzi, M.Si',
            'nip' => '198001012005011002',
            'is_active' => true,
        ]);

        $signer = CertificateSetting::where('nip', '198001012005011002')->first();
        $this->assertNotNull($signer->signature_image);
        Storage::disk('public')->assertExists($signer->signature_image);
    }

    public function test_admin_can_update_certificate_signer(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $signer = CertificateSetting::create([
            'name' => 'Nama Lama',
            'nip' => '1234567890',
            'jabatan' => 'Jabatan Lama',
            'instansi' => 'Instansi Lama',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.certificates.update', $signer->id), [
            'name' => 'AGUS SOFYAN, S.STP.,MPA',
            'nip' => '197908161998021001',
            'jabatan' => 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => 'Kabupaten Bengkalis',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.certificates.index'));
        $this->assertDatabaseHas('certificate_settings', [
            'id' => $signer->id,
            'name' => 'AGUS SOFYAN, S.STP.,MPA',
            'nip' => '197908161998021001',
        ]);
    }

    public function test_admin_can_delete_certificate_signer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $signer = CertificateSetting::create([
            'name' => 'Pejabat Sementara',
            'nip' => '9999999999',
            'jabatan' => 'Plt. Kadis',
            'instansi' => 'Kabupaten Bengkalis',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.certificates.destroy', $signer->id));
        $response->assertRedirect(route('admin.certificates.index'));

        $this->assertDatabaseMissing('certificate_settings', [
            'id' => $signer->id,
        ]);
    }

    public function test_admin_can_set_active_signer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $signer1 = CertificateSetting::create([
            'name' => 'Signer Satu',
            'nip' => '1111111111',
            'is_active' => true,
        ]);
        $signer2 = CertificateSetting::create([
            'name' => 'Signer Dua',
            'nip' => '2222222222',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.certificates.set-active', $signer2->id));
        $response->assertRedirect(route('admin.certificates.index'));

        $this->assertFalse($signer1->fresh()->is_active);
        $this->assertTrue($signer2->fresh()->is_active);
    }

    public function test_certificate_show_page_displays_active_signer_and_signature(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Meldi Adrian']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'duration' => 48,
            'certificate_enabled' => true,
        ]);

        $cert = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00001',
            'certificate_code' => 'BKS-M365-123456',
            'verification_url' => 'http://localhost/certificates/verify/BKS-M365-123456',
            'issued_at' => now(),
        ]);

        \App\Models\CourseProgress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 100.0,
            'status' => 'completed',
        ]);

        $signer = CertificateSetting::create([
            'name' => 'AGUS SOFYAN, S.STP.,MPA',
            'nip' => '197908161998021001',
            'jabatan' => 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => 'Kabupaten Bengkalis',
            'signature_image' => 'signatures/sample-ttd.png',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('certificates.show', $cert->id));
        $response->assertStatus(200);
        $response->assertSee('AGUS SOFYAN, S.STP.,MPA');
        $response->assertSee('197908161998021001');
        $response->assertSee('Kepala Dinas Komunikasi, Informatika dan Statistik');
        $response->assertSee('Kabupaten Bengkalis');
        $response->assertSee('signatures/sample-ttd.png');
    }
}
