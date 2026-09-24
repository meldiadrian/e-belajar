<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_faqs_page(): void
    {
        $publishedFaq = Faq::create([
            'question' => 'Apakah kursus gratis?',
            'answer' => 'Ya, seluruh kursus di E-Belajar Bengkalis gratis.',
            'category' => 'Umum',
            'order' => 1,
            'is_published' => true,
        ]);

        $draftFaq = Faq::create([
            'question' => 'Pertanyaan Rahasia?',
            'answer' => 'Jawaban rahasia internal.',
            'category' => 'Internal',
            'order' => 2,
            'is_published' => false,
        ]);

        $response = $this->get('/pertanyaan-umum');
        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Apakah kursus gratis?');
        $response->assertDontSee('Pertanyaan Rahasia?');
    }

    public function test_regular_user_cannot_access_admin_faqs(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/faqs');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_faqs_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $faq = Faq::create([
            'question' => 'Pertanyaan Admin',
            'answer' => 'Jawaban Admin',
            'category' => 'Umum',
            'order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/faqs');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pertanyaan Umum (FAQ)');
        $response->assertSee('Pertanyaan Admin');
    }

    public function test_admin_can_create_new_faq(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/faqs', [
            'question' => 'Bagaimana cara mendaftar akun?',
            'answer' => 'Klik tombol Daftar Sekarang di pojok kanan atas.',
            'category' => 'Akun',
            'order' => 1,
            'is_published' => '1',
        ]);

        $response->assertRedirect('/admin/faqs');
        $this->assertDatabaseHas('faqs', [
            'question' => 'Bagaimana cara mendaftar akun?',
            'category' => 'Akun',
            'is_published' => 1,
        ]);
    }

    public function test_admin_can_update_faq(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $faq = Faq::create([
            'question' => 'Pertanyaan Lama',
            'answer' => 'Jawaban Lama',
            'category' => 'Umum',
            'order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->put("/admin/faqs/{$faq->id}", [
            'question' => 'Pertanyaan Diperbarui',
            'answer' => 'Jawaban Diperbarui Lengkap',
            'category' => 'Sertifikat',
            'order' => 2,
            'is_published' => '1',
        ]);

        $response->assertRedirect('/admin/faqs');
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'Pertanyaan Diperbarui',
            'category' => 'Sertifikat',
            'order' => 2,
        ]);
    }

    public function test_admin_can_delete_faq(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $faq = Faq::create([
            'question' => 'Pertanyaan Dihapus',
            'answer' => 'Jawaban Dihapus',
            'category' => 'Umum',
            'order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/faqs/{$faq->id}");
        $response->assertRedirect('/admin/faqs');
        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id,
        ]);
    }

    public function test_admin_can_view_faq_categories_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        \App\Models\FaqCategory::create([
            'name' => 'Kategori Uji',
            'description' => 'Deskripsi Kategori',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/faq-categories');
        $response->assertStatus(200);
        $response->assertSee('Kategori Pertanyaan Umum (FAQ)');
        $response->assertSee('Kategori Uji');
    }

    public function test_admin_can_create_faq_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/faq-categories', [
            'name' => 'Kategori Baru',
            'description' => 'Penjelasan kategori',
            'order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/faq-categories');
        $this->assertDatabaseHas('faq_categories', [
            'name' => 'Kategori Baru',
            'slug' => 'kategori-baru',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_update_faq_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $category = \App\Models\FaqCategory::create([
            'name' => 'Kategori Lama',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put("/admin/faq-categories/{$category->id}", [
            'name' => 'Kategori Diedit',
            'description' => 'Deskripsi Baru',
            'order' => 3,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/faq-categories');
        $this->assertDatabaseHas('faq_categories', [
            'id' => $category->id,
            'name' => 'Kategori Diedit',
            'order' => 3,
        ]);
    }

    public function test_admin_can_delete_faq_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $category = \App\Models\FaqCategory::create([
            'name' => 'Kategori Hapus',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/faq-categories/{$category->id}");
        $response->assertRedirect('/admin/faq-categories');
        $this->assertDatabaseMissing('faq_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_regular_user_cannot_access_admin_faq_categories(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/faq-categories');
        $response->assertStatus(403);
    }
}
