<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\User;
use App\Services\CertificateService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_only_generated_when_course_progress_is_100_percent(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Pelayanan Publik Prima',
            'slug' => 'pelayanan-publik-prima',
            'certificate_enabled' => true,
        ]);

        $certificateService = app(CertificateService::class);

        // Case 1: Incomplete progress (50%) -> should throw Exception
        CourseProgress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 50.0,
            'completed_lessons' => 1,
            'total_lessons' => 2,
            'status' => 'in_progress',
        ]);

        $this->expectException(Exception::class);
        $certificateService->generateCertificate($user, $course);
    }

    public function test_certificate_generated_successfully_at_100_percent_and_can_be_publicly_verified(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Budi Santoso']);
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Pelayanan Publik Prima',
            'slug' => 'pelayanan-publik-prima',
            'certificate_enabled' => true,
        ]);

        CourseProgress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percentage' => 100.0,
            'completed_lessons' => 2,
            'total_lessons' => 2,
            'status' => 'completed',
        ]);

        $certificateService = app(CertificateService::class);
        $cert = $certificateService->generateCertificate($user, $course);

        $this->assertNotNull($cert);
        $this->assertStringStartsWith('CERT/BKS/', $cert->certificate_number);
        $this->assertStringStartsWith('BKS-', $cert->certificate_code);

        // Public verification test
        $response = $this->getJson("/api/certificates/verify/{$cert->certificate_code}");
        $response->assertStatus(200);
        $response->assertJson([
            'is_valid' => true,
            'certificate_code' => $cert->certificate_code,
            'recipient_name' => 'Budi Santoso',
            'course_title' => 'Pelayanan Publik Prima',
        ]);

        // Non-existent certificate verification
        $invalidResponse = $this->getJson("/api/certificates/verify/FAKE-CODE-12345");
        $invalidResponse->assertStatus(404);
        $invalidResponse->assertJson(['is_valid' => false]);
    }
}
