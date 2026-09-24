<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enroll_in_published_course(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Digitalisasi Desa Bengkalis',
            'slug' => 'digitalisasi-desa',
            'status' => 'published',
            'level' => 'beginner',
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Pengenalan Sistem',
            'slug' => 'pengenalan-sistem',
            'lesson_type' => 'text',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->post("/courses/{$course->id}/enroll");

        $response->assertRedirect("/learning/{$course->id}/lesson/{$lesson->id}");
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('course_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'not_started',
        ]);
    }

    public function test_user_cannot_enroll_in_the_same_course_twice(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Digitalisasi Desa Bengkalis',
            'slug' => 'digitalisasi-desa',
            'status' => 'published',
            'level' => 'beginner',
        ]);

        // First enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Attempt second enrollment via API
        $response = $this->actingAs($user)->postJson("/api/courses/{$course->id}/enroll");

        $response->assertStatus(422);
        $this->assertEquals(1, Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }
}
