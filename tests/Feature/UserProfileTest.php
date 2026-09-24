<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile_edit_page(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Budi Santoso',
        ]);

        $response = $this->actingAs($user)->get("/profile/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Edit Profil');
    }

    public function test_user_can_update_own_profile_based_on_user_id(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Nama Lama',
            'phone' => '0811111111',
            'institution' => 'Instansi Lama',
        ]);

        $response = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'Nama Baru Bengkalis',
            'email' => $user->email,
            'phone' => '0899999999',
            'institution' => 'Dinas Pendidikan Bengkalis',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Bengkalis',
            'phone' => '0899999999',
            'institution' => 'Dinas Pendidikan Bengkalis',
        ]);
    }

    public function test_user_cannot_edit_another_users_profile(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        // User 1 attempts to view User 2's profile edit page
        $getResponse = $this->actingAs($user1)->get("/profile/{$user2->id}");
        $getResponse->assertStatus(403);

        // User 1 attempts to update User 2's profile
        $putResponse = $this->actingAs($user1)->put("/profile/{$user2->id}", [
            'name' => 'Hacked Name',
            'email' => $user2->email,
        ]);
        $putResponse->assertStatus(403);
    }

    public function test_cancel_and_return_to_dashboard_from_profile_loads_safely(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = \App\Models\Category::create(['name' => 'Gov', 'slug' => 'gov']);
        $course = \App\Models\Course::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Kursus Admin Terhapus',
            'slug' => 'kursus-admin-terhapus',
            'status' => 'published',
            'level' => 'beginner',
        ]);

        \App\Models\Enrollment::create([
            'user_id' => $admin->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $course->delete();

        // Admin opens profile edit page
        $profileResponse = $this->actingAs($admin)->get("/profile/{$admin->id}");
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Batal & Kembali ke Dashboard', false);

        // Clicking "Batal & Kembali ke Dashboard" accesses /dashboard
        $dashboardResponse = $this->actingAs($admin)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Pendaftaran Peserta Terbaru');
    }
}
