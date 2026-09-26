<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

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

    public function test_admin_can_view_own_profile_in_admin_layout(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Diskominfotik',
        ]);

        $response = $this->actingAs($admin)->get("/profile/{$admin->id}");

        $response->assertStatus(200);
        $response->assertSee('Admin Diskominfotik');
        $response->assertSee('Edit Profil & Kata Sandi');
        $response->assertSee('E-Belajar Panel');
        $response->assertSee('Admin Kursus');
    }

    public function test_admin_can_update_own_profile_and_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($admin)->put("/profile/{$admin->id}", [
            'name' => 'Admin Bengkalis Updated',
            'email' => 'admin.updated@bengkalis.go.id',
            'institution' => 'Diskominfotik Bengkalis',
            'phone' => '081234567890',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Bengkalis Updated',
            'email' => 'admin.updated@bengkalis.go.id',
            'institution' => 'Diskominfotik Bengkalis',
            'phone' => '081234567890',
        ]);

        $this->assertTrue(Hash::check('newpassword123', $admin->fresh()->password));
    }

    public function test_admin_can_upload_avatar_photo_profil(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($admin)->put("/profile/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');
        $updatedUser = $admin->fresh();
        $this->assertNotNull($updatedUser->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($updatedUser->avatar);
    }

    public function test_superadmin_can_update_own_profile_password_and_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'password' => Hash::make('supersecret123'),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('superavatar.png', 200, 200);

        $response = $this->actingAs($superadmin)->put("/profile/{$superadmin->id}", [
            'name' => 'Superadmin Utama Bengkalis',
            'email' => 'superadmin@bengkalis.go.id',
            'institution' => 'Bupati Bengkalis',
            'password' => 'supernewpass123',
            'password_confirmation' => 'supernewpass123',
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');
        $freshSuperadmin = $superadmin->fresh();
        $this->assertEquals('Superadmin Utama Bengkalis', $freshSuperadmin->name);
        $this->assertTrue(Hash::check('supernewpass123', $freshSuperadmin->password));
        $this->assertNotNull($freshSuperadmin->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($freshSuperadmin->avatar);
    }

    public function test_admin_cannot_edit_another_users_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['role' => 'user']);

        $getResponse = $this->actingAs($admin)->get("/profile/{$otherUser->id}");
        $getResponse->assertStatus(403);

        $putResponse = $this->actingAs($admin)->put("/profile/{$otherUser->id}", [
            'name' => 'Unauthorized Admin Update',
            'email' => $otherUser->email,
        ]);
        $putResponse->assertStatus(403);
    }

    public function test_user_can_update_nip_nik_tempat_lahir_and_agama_in_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'nip' => '199001012015011001',
            'nik' => '1403010101900001',
            'tempat_lahir' => 'Bengkalis',
            'agama' => 'Islam',
        ]);

        $response = $this->actingAs($user)->get("/profile/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee('199001012015011001');
        $response->assertSee('1403010101900001');
        $response->assertSee('Bengkalis');
        $response->assertSee('Islam');

        $updateResponse = $this->actingAs($user)->put("/profile/{$user->id}", [
            'name' => 'Aparatur Bengkalis',
            'email' => $user->email,
            'nip' => '199505052020011005',
            'nik' => '1403020202950002',
            'tempat_lahir' => 'Duri',
            'agama' => 'Islam',
            'institution' => 'Bappeda Bengkalis',
            'phone' => '081234567899',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nip' => '199505052020011005',
            'nik' => '1403020202950002',
            'tempat_lahir' => 'Duri',
            'agama' => 'Islam',
        ]);
    }
}
