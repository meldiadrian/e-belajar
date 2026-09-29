<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
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

        \App\Models\Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'status' => 'active',
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

    public function test_admin_can_view_create_page_with_auto_generated_next_certificate_number(): void
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

        // When no certificate exists, default starts with CERT/BKS/
        $response = $this->actingAs($admin)->get(route('admin.issued-certificates.create'));
        $response->assertStatus(200);
        $response->assertSee('CERT/BKS/');

        // If a certificate exists with custom pattern
        Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'certificate_code' => 'BKS-TEST-123456',
            'verification_url' => 'http://localhost/certificates/verify/BKS-TEST-123456',
            'issued_at' => now(),
        ]);

        // Next certificate should automatically increment to BKPP-PKA/2026/00002
        $response2 = $this->actingAs($admin)->get(route('admin.issued-certificates.create'));
        $response2->assertStatus(200);
        $response2->assertSee('BKPP-PKA/2026/00002');
    }

    public function test_admin_can_input_certificate_via_crud_and_next_index_is_automatic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student1 = User::factory()->create(['role' => 'user', 'name' => 'Peserta Satu']);
        $student2 = User::factory()->create(['role' => 'user', 'name' => 'Peserta Dua']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        // 1. Input the first certificate with custom format
        $response1 = $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'user_id' => $student1->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);

        $response1->assertRedirect(route('admin.issued-certificates.index'));
        $response1->assertSessionHas('success');

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student1->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
        ]);

        // 2. Next certificate number should automatically be 00002
        $this->assertEquals('BKPP-PKA/2026/00002', Certificate::getNextCertificateNumber());

        // 3. Input second certificate using the automatic next number
        $response2 = $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'certificate_number' => Certificate::getNextCertificateNumber(),
            'issued_at' => '2026-09-29',
        ]);

        $response2->assertRedirect(route('admin.issued-certificates.index'));
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student2->id,
            'certificate_number' => 'BKPP-PKA/2026/00002',
        ]);

        // 4. Third next number automatically becomes 00003
        $this->assertEquals('BKPP-PKA/2026/00003', Certificate::getNextCertificateNumber());
    }

    public function test_create_page_displays_only_role_user_in_recipient_select(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User Super']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Peserta Pelatihan']);

        $response = $this->actingAs($admin)->get(route('admin.issued-certificates.create'));
        $response->assertStatus(200);

        // Shows Role User option
        $response->assertSee('Role User');

        // Does NOT display individual user ids in options
        $response->assertDontSee('<option value="' . $student->id . '"', false);
        $response->assertDontSee('<option value="' . $admin->id . '"', false);
    }

    public function test_all_role_users_automatically_follow_certificate_number_sequentially(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student1 = User::factory()->create(['role' => 'user', 'name' => 'Peserta A']);
        $student2 = User::factory()->create(['role' => 'user', 'name' => 'Peserta B']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'role' => 'user',
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);

        $response->assertRedirect(route('admin.issued-certificates.index'));
        $response->assertSessionHas('success');

        // Student 1 has 00001
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student1->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
        ]);

        // Student 2 has 00002
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00002',
        ]);

        // Admin does not get a certificate
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $admin->id,
        ]);
    }

    public function test_different_courses_can_use_the_same_certificate_number_starting_from_the_beginning(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student1 = User::factory()->create(['role' => 'user', 'name' => 'Peserta Satu']);
        $student2 = User::factory()->create(['role' => 'user', 'name' => 'Peserta Dua']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $course1 = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $course2 = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Belajar Neraca Keuangan',
            'slug' => 'belajar-neraca-keuangan',
            'certificate_enabled' => true,
        ]);

        // Course 1 uses BKPP-PKA/2026/00001
        $res1 = $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'user_id' => $student1->id,
            'course_id' => $course1->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);
        $res1->assertRedirect(route('admin.issued-certificates.index'));
        $res1->assertSessionHas('success');

        // Course 2 (kursus pelatihan lainnya) is allowed to use the same certificate number starting from the beginning
        $res2 = $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'user_id' => $student2->id,
            'course_id' => $course2->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);
        $res2->assertRedirect(route('admin.issued-certificates.index'));
        $res2->assertSessionHas('success');

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student1->id,
            'course_id' => $course1->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
        ]);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student2->id,
            'course_id' => $course2->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
        ]);
    }

    public function test_certificate_is_not_displayed_in_role_user_until_course_and_quiz_completed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Peserta Pelatihan']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Evaluasi Pemahaman',
            'passing_score' => 70,
            'is_published' => true,
        ]);

        // Admin registers certificate number
        $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'role' => 'user',
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);

        $cert = Certificate::where('user_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($cert);

        // Student logs in as role 'user' WITHOUT completing course or passing quiz
        // 1. In /my/certificates: Certificate must NOT be displayed
        $certPageResponse = $this->actingAs($student)->get(route('my.certificates'));
        $certPageResponse->assertStatus(200);
        $certPageResponse->assertDontSee($cert->certificate_number);
        $certPageResponse->assertSee('Belum ada sertifikat yang diterbitkan');

        // 2. In /dashboard: Total certificates must be 0 and certificate not listed
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertDontSee($cert->certificate_number);

        // 3. Trying to access certificate show directly: Must be redirected with warning
        $showResponse = $this->actingAs($student)->get(route('certificates.show', $cert->id));
        $showResponse->assertRedirect(route('learning.course', $course->slug));
        $showResponse->assertSessionHas('error');
    }

    public function test_certificate_is_displayed_in_role_user_after_course_and_quiz_completed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Peserta Pelatihan']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Microsoft 365',
            'slug' => 'microsoft-365',
            'certificate_enabled' => true,
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Evaluasi Pemahaman',
            'passing_score' => 70,
            'is_published' => true,
        ]);

        // Admin registers certificate number
        $this->actingAs($admin)->post(route('admin.issued-certificates.store'), [
            'role' => 'user',
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'issued_at' => '2026-09-29',
        ]);

        $cert = Certificate::where('user_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($cert);

        // Now student follows and completes the course (100%) and passes the quiz
        CourseProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress_percentage' => 100.0,
            'completed_lessons' => 5,
            'total_lessons' => 5,
            'status' => 'completed',
        ]);

        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 85,
            'max_score' => 100,
            'percentage' => 85,
            'passed' => true,
            'status' => 'submitted',
        ]);

        // 1. In /my/certificates: Certificate must be displayed
        $certPageResponse = $this->actingAs($student)->get(route('my.certificates'));
        $certPageResponse->assertStatus(200);
        $certPageResponse->assertSee($cert->certificate_number);
        $certPageResponse->assertSee('Sertifikat Sah');

        // 2. In /dashboard: Total certificates must be 1 and certificate listed
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee($cert->certificate_code);

        // 3. Accessing certificate show directly: Must be successful (200)
        $showResponse = $this->actingAs($student)->get(route('certificates.show', $cert->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($cert->certificate_number);
    }

    public function test_certificates_of_users_who_have_not_followed_the_class_do_not_appear_in_admin_list_view(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentEnrolled = User::factory()->create(['role' => 'user', 'name' => 'Peserta Aktif']);
        $studentNotEnrolled = User::factory()->create(['role' => 'user', 'name' => 'Peserta Belum Ikut']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Belajar Neraca Keuangan',
            'slug' => 'belajar-neraca-keuangan',
            'certificate_enabled' => true,
        ]);

        // Student 1 is enrolled in course
        \App\Models\Enrollment::create([
            'user_id' => $studentEnrolled->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        // Student 2 is NOT enrolled in course

        // Both have certificates created
        $cert1 = Certificate::create([
            'user_id' => $studentEnrolled->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00001',
            'certificate_code' => 'BKS-ENROLLED-1',
            'verification_url' => 'http://localhost/certificates/verify/BKS-ENROLLED-1',
            'issued_at' => now(),
        ]);

        $cert2 = Certificate::create([
            'user_id' => $studentNotEnrolled->id,
            'course_id' => $course->id,
            'certificate_number' => 'BKPP-PKA/2026/00002',
            'certificate_code' => 'BKS-NOTENROLLED-2',
            'verification_url' => 'http://localhost/certificates/verify/BKS-NOTENROLLED-2',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.issued-certificates.index'));
        $response->assertStatus(200);

        // Certificate 1 (enrolled student) MUST be visible in admin list view
        $response->assertSee('BKPP-PKA/2026/00001');
        $response->assertSee('Peserta Aktif');

        // Certificate 2 (student who has NOT followed the class) MUST NOT be visible in admin list view
        $response->assertDontSee('BKPP-PKA/2026/00002');
        $response->assertDontSee('Peserta Belum Ikut');
    }
}



