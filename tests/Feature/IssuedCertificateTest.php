<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssuedCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_issued_certificates_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.issued-certificates.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_issued_certificates_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Meldi Adrian', 'nip' => '123456789']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $cert = Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00001',
            'certificate_code' => 'BKS-TEST-123456',
            'verification_url' => 'http://localhost/certificates/verify/BKS-TEST-123456',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.issued-certificates.index'));
        $response->assertStatus(200);
        $response->assertSee('CERT/BKS/2026/09/00001');
        $response->assertSee('Meldi Adrian');
        $response->assertSee('Microsoft 365');
    }

    public function test_admin_can_update_certificate_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Meldi Adrian']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $cert = Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00001',
            'certificate_code' => 'BKS-TEST-123456',
            'verification_url' => 'http://localhost/certificates/verify/BKS-TEST-123456',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.issued-certificates.update', $cert->id), [
            'certificate_number' => 'CERT/BKS/2026/09/99999',
            'issued_at' => '2026-09-28',
        ]);

        $response->assertRedirect(route('admin.issued-certificates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('certificates', [
            'id' => $cert->id,
            'certificate_number' => 'CERT/BKS/2026/09/99999',
        ]);
    }

    public function test_duplicate_certificate_number_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $cert1 = Certificate::create([
            'user_id' => $user1->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00001',
            'certificate_code' => 'BKS-CERT-111111',
            'verification_url' => 'http://localhost/certificates/verify/BKS-CERT-111111',
            'issued_at' => now(),
        ]);

        $cert2 = Certificate::create([
            'user_id' => $user2->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00002',
            'certificate_code' => 'BKS-CERT-222222',
            'verification_url' => 'http://localhost/certificates/verify/BKS-CERT-222222',
            'issued_at' => now(),
        ]);

        // Trying to update cert2 with cert1's certificate_number
        $response = $this->actingAs($admin)->put(route('admin.issued-certificates.update', $cert2->id), [
            'certificate_number' => 'CERT/BKS/2026/09/00001',
        ]);

        $response->assertSessionHasErrors('certificate_number');
    }

    public function test_admin_can_delete_issued_certificate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $cert = Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_number' => 'CERT/BKS/2026/09/00001',
            'certificate_code' => 'BKS-TEST-123456',
            'verification_url' => 'http://localhost/certificates/verify/BKS-TEST-123456',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.issued-certificates.destroy', $cert->id));
        $response->assertRedirect(route('admin.issued-certificates.index'));

        $this->assertDatabaseMissing('certificates', [
            'id' => $cert->id,
        ]);
    }
}
