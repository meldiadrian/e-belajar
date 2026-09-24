<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Web route test
        $webResponse = $this->actingAs($user)->get('/admin/courses');
        $webResponse->assertStatus(403);

        // API route test
        $apiResponse = $this->actingAs($user)->postJson('/api/courses', [
            'title' => 'Unauthorized Course',
        ]);
        $apiResponse->assertStatus(403);
    }

    public function test_admin_can_crud_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it']);

        // Create
        $response = $this->actingAs($admin)->post('/admin/courses', [
            'category_id' => $category->id,
            'title' => 'Kursus Admin Baru',
            'level' => 'beginner',
            'status' => 'draft',
            'duration' => 60,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('courses', ['title' => 'Kursus Admin Baru']);

        $course = Course::where('title', 'Kursus Admin Baru')->first();

        // Update
        $updateResponse = $this->actingAs($admin)->put("/admin/courses/{$course->id}", [
            'title' => 'Kursus Admin Diperbarui',
            'level' => 'intermediate',
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('courses', ['title' => 'Kursus Admin Diperbarui']);

        // Delete (Soft Delete)
        $deleteResponse = $this->actingAs($admin)->delete("/admin/courses/{$course->id}");
        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_superadmin_can_access_all_resources_including_users_and_activity_logs(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $usersResponse = $this->actingAs($superadmin)->get('/superadmin/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('Basis Data Pengguna Sistem');

        $logsResponse = $this->actingAs($superadmin)->get('/superadmin/activity-logs');
        $logsResponse->assertStatus(200);
        $logsResponse->assertSee('Jejak Audit Aktivitas Sistem');
    }

    public function test_admin_can_update_and_delete_quiz(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Bisnis', 'slug' => 'bisnis']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Kursus Kuis Test',
            'slug' => 'kursus-kuis-test',
            'status' => 'draft',
        ]);

        $quiz = \App\Models\Quiz::create([
            'course_id' => $course->id,
            'title' => 'Kuis Lama',
            'passing_score' => 70,
            'time_limit' => 15,
            'max_attempts' => 3,
        ]);

        // Edit / Update quiz
        $updateResponse = $this->actingAs($admin)->put("/admin/quizzes/{$quiz->id}", [
            'title' => 'Kuis Baru Diperbarui',
            'passing_score' => 80,
            'time_limit' => 20,
            'max_attempts' => 2,
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'Kuis Baru Diperbarui',
            'passing_score' => 80,
        ]);

        // Delete quiz
        $deleteResponse = $this->actingAs($admin)->delete("/admin/quizzes/{$quiz->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }

    public function test_admin_can_update_lesson_and_question(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Design', 'slug' => 'design']);

        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Kursus Multimedia',
            'slug' => 'kursus-multimedia',
            'level' => 'beginner',
            'status' => 'published',
            'duration' => 60,
        ]);

        $module = \App\Models\CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul 1',
            'sort_order' => 1,
        ]);

        $lesson = \App\Models\Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Video Lama',
            'slug' => 'video-lama',
            'lesson_type' => 'video',
            'duration' => 300,
            'sort_order' => 1,
        ]);

        // Update lesson
        $updateLessonResp = $this->actingAs($admin)->put("/admin/lessons/{$lesson->id}", [
            'title' => 'Video Baru Diperbarui',
            'lesson_type' => 'video',
            'duration' => 600,
            'video_url' => 'https://www.youtube.com/watch?v=newvideoid',
        ]);
        $updateLessonResp->assertSessionHas('success');
        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'Video Baru Diperbarui',
            'duration' => 600,
        ]);
        $this->assertDatabaseHas('lesson_contents', [
            'lesson_id' => $lesson->id,
            'type' => 'video',
            'url' => 'https://www.youtube.com/watch?v=newvideoid',
        ]);

        // Create Quiz & Question
        $quiz = \App\Models\Quiz::create([
            'course_id' => $course->id,
            'title' => 'Kuis Test',
            'passing_score' => 70,
        ]);

        $question = \App\Models\Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Soal Lama?',
            'type' => 'single_choice',
            'points' => 10,
            'sort_order' => 1,
        ]);

        \App\Models\QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Opsi A',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        // Update Question
        $updateQuestionResp = $this->actingAs($admin)->put("/admin/questions/{$question->id}", [
            'question' => 'Soal Baru Diperbarui?',
            'type' => 'single_choice',
            'points' => 20,
            'explanation' => 'Penjelasan baru',
            'correct_option' => 1,
            'options' => [
                ['text' => 'Pilihan 1'],
                ['text' => 'Pilihan 2'],
            ],
        ]);
        $updateQuestionResp->assertSessionHas('success');
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question' => 'Soal Baru Diperbarui?',
            'points' => 20,
            'explanation' => 'Penjelasan baru',
        ]);
        $this->assertDatabaseHas('question_options', [
            'question_id' => $question->id,
            'option_text' => 'Pilihan 2',
            'is_correct' => true,
        ]);
    }

    public function test_superadmin_can_create_user_and_records_activity(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->post('/superadmin/users', [
            'name' => 'User Baru Uji',
            'email' => 'user_baru_uji@bengkalis.go.id',
            'password' => 'password123',
            'role' => 'user',
            'institution' => 'Diskominfotik',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru Uji',
            'email' => 'user_baru_uji@bengkalis.go.id',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'user_created',
        ]);
    }

    public function test_superadmin_can_recreate_previously_deleted_user_email(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $user = User::factory()->create([
            'email' => 'terhapus@bengkalis.go.id',
            'role' => 'user',
        ]);
        $user->delete(); // soft delete

        $response = $this->actingAs($superadmin)->post('/superadmin/users', [
            'name' => 'User Baru Email Sama',
            'email' => 'terhapus@bengkalis.go.id',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'User Baru Email Sama',
            'email' => 'terhapus@bengkalis.go.id',
            'role' => 'admin',
            'deleted_at' => null,
        ]);
    }
}
