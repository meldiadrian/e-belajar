<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonProgress;
use App\Models\Notification;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\Tag;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\CertificateService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic Bengkalis data.
     */
    public function run(): void
    {
        // 1. Roles & Users
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@bengkalis.go.id'],
            [
                'name' => 'Super Administrator Bengkalis',
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'password123')),
                'role' => 'superadmin',
                'institution' => 'Diskominfotik Kabupaten Bengkalis',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@bengkalis.go.id'],
            [
                'name' => 'Admin Pengelola Kursus',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
                'role' => 'admin',
                'institution' => 'BKPSDM Kabupaten Bengkalis',
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $peserta1 = User::firstOrCreate(
            ['email' => 'peserta1@bengkalis.go.id'],
            [
                'name' => 'Ahmad Fauzi, S.Kom',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'institution' => 'Dinas Pendidikan Kabupaten Bengkalis',
                'phone' => '081298765432',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $peserta2 = User::firstOrCreate(
            ['email' => 'peserta2@bengkalis.go.id'],
            [
                'name' => 'Siti Rahmawati, S.AP',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'institution' => 'Bappeda Kabupaten Bengkalis',
                'phone' => '081298765433',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Transformasi digital, sistem informasi, dan literasi teknologi pemerintahan.'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Metodologi pengajaran, kurikulum terpadu, dan pelatihan tenaga pendidik.'],
            ['name' => 'Administrasi', 'slug' => 'administrasi', 'description' => 'Tata kelola persuratan, kearsipan, dan efektivitas administrasi publik.'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'description' => 'Peningkatan mutu layanan kesehatan puskesmas dan pencegahan penyakit.'],
            ['name' => 'Keuangan', 'slug' => 'keuangan', 'description' => 'Akuntabilitas penatausahaan anggaran, perpajakan, dan aset daerah.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::firstOrCreate(['slug' => $cData['slug']], $cData);
        }

        // 3. Tags
        $tagsData = ['Pelayanan Publik', 'Transformasi Digital', 'Tata Kelola', 'Kompetensi ASN'];
        $tags = [];
        foreach ($tagsData as $tName) {
            $tags[] = Tag::firstOrCreate(['slug' => Str::slug($tName)], ['name' => $tName]);
        }

        // 4. Primary Course 1: SPBE Kabupaten Bengkalis
        $course1 = Course::firstOrCreate(
            ['slug' => 'spbe-kabupaten-bengkalis'],
            [
                'category_id' => $categories['teknologi']->id,
                'created_by' => $admin->id,
                'title' => 'Tata Kelola Sistem Pemerintahan Berbasis Elektronik (SPBE) Kabupaten Bengkalis',
                'description' => 'Panduan komprehensif implementasi SPBE di lingkungan Pemerintah Kabupaten Bengkalis sesuai Perpres No. 95 Tahun 2018 untuk mewujudkan birokrasi yang lincah, transparan, dan terintegrasi.',
                'level' => 'intermediate',
                'status' => 'published',
                'duration' => 120,
                'certificate_enabled' => true,
                'published_at' => now(),
            ]
        );
        $course1->tags()->sync(collect($tags)->pluck('id'));

        // Module 1
        $module1 = CourseModule::firstOrCreate(
            ['course_id' => $course1->id, 'title' => 'Modul 1: Kerangka Kerja dan Arsitektur SPBE'],
            ['description' => 'Prinsip dasar dan domain utama arsitektur SPBE daerah.', 'sort_order' => 1, 'is_published' => true]
        );

        // Lesson 1: Text
        $lesson1 = Lesson::firstOrCreate(
            ['course_module_id' => $module1->id, 'slug' => 'pengantar-arsitektur-spbe-bengkalis'],
            [
                'title' => 'Pengenalan Arsitektur SPBE Kabupaten Bengkalis',
                'lesson_type' => 'text',
                'content' => "Sistem Pemerintahan Berbasis Elektronik (SPBE) adalah penyelenggaraan pemerintahan yang memanfaatkan teknologi informasi dan komunikasi untuk memberikan layanan kepada pengguna SPBE.\n\nDalam rangka mewujudkan tata kelola pemerintahan yang bersih, efektif, transparan, dan akuntabel serta pelayanan publik yang berkualitas dan terpercaya di Kabupaten Bengkalis, penerapan SPBE menjadi fondasi utama transformasi birokrasi daerah.\n\nEnam domain utama SPBE meliputi:\n1. Domain Kebijakan SPBE Internal\n2. Domain Tata Kelola SPBE\n3. Domain Manajemen SPBE\n4. Domain Layanan SPBE Administrasi Pemerintahan\n5. Domain Layanan Publik SPBE\n6. Domain Keamanan Informasi dan Audit TIK",
                'duration' => 600,
                'sort_order' => 1,
                'is_preview' => true,
                'is_published' => true,
            ]
        );

        // Lesson 2: Video
        $lesson2 = Lesson::firstOrCreate(
            ['course_module_id' => $module1->id, 'slug' => 'video-layanan-digital-terpadu'],
            [
                'title' => 'Video Pembelajaran: Integrasi Layanan Publik Terpadu',
                'lesson_type' => 'video',
                'content' => "Simak video penjelasan mengenai interoperabilitas antarsistem aplikasi di OPD Kabupaten Bengkalis dan implementasi Satu Data Indonesia.",
                'duration' => 900,
                'sort_order' => 2,
                'is_preview' => false,
                'is_published' => true,
            ]
        );

        LessonContent::firstOrCreate(
            ['lesson_id' => $lesson2->id, 'type' => 'video'],
            [
                'title' => 'Video Paparan Integrasi Layanan SPBE',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sort_order' => 1,
            ]
        );

        // Lesson 3: Document
        $lesson3 = Lesson::firstOrCreate(
            ['course_module_id' => $module1->id, 'slug' => 'dokumen-pedoman-spbe-bengkalis'],
            [
                'title' => 'Dokumen Pedoman Kebijakan SPBE Bengkalis',
                'lesson_type' => 'document',
                'content' => "Bahan bacaan resmi: Surat Edaran dan Petunjuk Teknis Keamanan Informasi serta Tata Kelola Data Kabupaten Bengkalis.",
                'duration' => 600,
                'sort_order' => 3,
                'is_preview' => false,
                'is_published' => true,
            ]
        );

        LessonContent::firstOrCreate(
            ['lesson_id' => $lesson3->id, 'type' => 'document'],
            [
                'title' => 'Pedoman_Teknis_SPBE_Bengkalis.pdf',
                'file_name' => 'Pedoman_Teknis_SPBE_Bengkalis.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1024000,
                'url' => 'https://bengkalis.go.id',
                'sort_order' => 1,
            ]
        );

        // Quiz for Course 1
        $quiz1 = Quiz::firstOrCreate(
            ['course_id' => $course1->id, 'title' => 'Asesmen Evaluasi Mandiri SPBE'],
            [
                'lesson_id' => $lesson3->id,
                'description' => 'Evaluasi pemahaman konsep arsitektur dan kebijakan SPBE Kabupaten Bengkalis.',
                'time_limit' => 15,
                'passing_score' => 70,
                'max_attempts' => 3,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'is_published' => true,
            ]
        );

        // Question 1: Single Choice
        $q1 = Question::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'question' => 'Apa tujuan utama dari penyelenggaraan SPBE di lingkungan Pemerintah Kabupaten Bengkalis?'],
            [
                'type' => 'single_choice',
                'points' => 40,
                'sort_order' => 1,
                'explanation' => 'Tujuan utama SPBE adalah mewujudkan tata kelola pemerintahan yang bersih, transparan, dan pelayanan publik berkualitas tinggi.',
            ]
        );

        QuestionOption::firstOrCreate(['question_id' => $q1->id, 'option_text' => 'Mewujudkan tata kelola pemerintahan yang bersih, transparan, akuntabel, dan pelayanan publik berkualitas'], ['is_correct' => true, 'sort_order' => 1]);
        QuestionOption::firstOrCreate(['question_id' => $q1->id, 'option_text' => 'Mengganti seluruh pegawai negeri dengan sistem otomatisasi kecerdasan buatan'], ['is_correct' => false, 'sort_order' => 2]);
        QuestionOption::firstOrCreate(['question_id' => $q1->id, 'option_text' => 'Membeli perangkat keras komputer sebanyak mungkin di setiap dinas'], ['is_correct' => false, 'sort_order' => 3]);
        QuestionOption::firstOrCreate(['question_id' => $q1->id, 'option_text' => 'Menghilangkan seluruh proses arsip fisik secara mendadak'], ['is_correct' => false, 'sort_order' => 4]);

        // Question 2: True/False
        $q2 = Question::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'question' => 'Interoperabilitas dan bagi pakai data antar-OPD adalah salah satu pilar krusial dalam SPBE.'],
            [
                'type' => 'true_false',
                'points' => 30,
                'sort_order' => 2,
                'explanation' => 'Benar, interoperabilitas data menghapus silo data antardinas sehingga layanan publik terintegrasi.',
            ]
        );

        QuestionOption::firstOrCreate(['question_id' => $q2->id, 'option_text' => 'Benar (True)'], ['is_correct' => true, 'sort_order' => 1]);
        QuestionOption::firstOrCreate(['question_id' => $q2->id, 'option_text' => 'Salah (False)'], ['is_correct' => false, 'sort_order' => 2]);

        // Question 3: Multiple Choice
        $q3 = Question::firstOrCreate(
            ['quiz_id' => $quiz1->id, 'question' => 'Manakah faktor yang menentukan keberhasilan penerapan SPBE di daerah?'],
            [
                'type' => 'single_choice',
                'points' => 30,
                'sort_order' => 3,
                'explanation' => 'Komitmen pimpinan, regulasi yang kuat, dan kesiapan kompetensi SDM adalah faktor kunci.',
            ]
        );

        QuestionOption::firstOrCreate(['question_id' => $q3->id, 'option_text' => 'Komitmen pimpinan, payung regulasi, dan peningkatan kompetensi SDM aparatur'], ['is_correct' => true, 'sort_order' => 1]);
        QuestionOption::firstOrCreate(['question_id' => $q3->id, 'option_text' => 'Menutup keterbukaan informasi dari masyarakat umum'], ['is_correct' => false, 'sort_order' => 2]);
        QuestionOption::firstOrCreate(['question_id' => $q3->id, 'option_text' => 'Penggunaan sistem yang tidak terhubung dengan jaringan internet'], ['is_correct' => false, 'sort_order' => 3]);

        // 5. Course 2: Administrasi Publik Desa
        $course2 = Course::firstOrCreate(
            ['slug' => 'administrasi-keuangan-desa'],
            [
                'category_id' => $categories['keuangan']->id,
                'created_by' => $admin->id,
                'title' => 'Tata Kelola dan Akuntabilitas Penatausahaan Keuangan Desa',
                'description' => 'Pelatihan praktis penyusunan APBDes, pertanggungjawaban anggaran, dan pemanfaatan sistem Siskeudes terstandar.',
                'level' => 'beginner',
                'status' => 'published',
                'duration' => 90,
                'certificate_enabled' => true,
                'published_at' => now(),
            ]
        );

        $module2 = CourseModule::firstOrCreate(
            ['course_id' => $course2->id, 'title' => 'Modul 1: Siklus Perencanaan Anggaran Desa'],
            ['sort_order' => 1, 'is_published' => true]
        );

        Lesson::firstOrCreate(
            ['course_module_id' => $module2->id, 'slug' => 'perencanaan-apbdes-partisipatif'],
            [
                'title' => 'Penyusunan APBDes Partisipatif dan Transparan',
                'lesson_type' => 'text',
                'content' => 'Prinsip transparansi dalam perencanaan anggaran desa melibatkan musrenbangdes dan keterbukaan informasi kepada warga desa.',
                'duration' => 600,
                'sort_order' => 1,
                'is_preview' => true,
                'is_published' => true,
            ]
        );

        // 6. Seed Enrollment & 100% Completion + Certificate for Peserta 1 on Course 1
        $enrollment1 = Enrollment::firstOrCreate(
            ['user_id' => $peserta1->id, 'course_id' => $course1->id],
            [
                'enrolled_at' => now()->subDays(3),
                'started_at' => now()->subDays(3),
                'completed_at' => now()->subHour(),
                'status' => 'completed',
            ]
        );

        // Mark all 3 lessons completed for Peserta 1
        foreach ([$lesson1, $lesson2, $lesson3] as $les) {
            LessonProgress::updateOrCreate(
                ['user_id' => $peserta1->id, 'lesson_id' => $les->id],
                [
                    'status' => 'completed',
                    'progress_percentage' => 100.0,
                    'watch_seconds' => $les->duration,
                    'started_at' => now()->subDays(2),
                    'completed_at' => now()->subHour(),
                ]
            );
        }

        // Course Progress 100%
        CourseProgress::updateOrCreate(
            ['user_id' => $peserta1->id, 'course_id' => $course1->id],
            [
                'total_lessons' => 3,
                'completed_lessons' => 3,
                'progress_percentage' => 100.0,
                'started_at' => now()->subDays(3),
                'completed_at' => now()->subHour(),
                'last_accessed_at' => now(),
                'status' => 'completed',
            ]
        );

        // Passed Quiz Attempt for Peserta 1
        $attempt1 = QuizAttempt::firstOrCreate(
            ['user_id' => $peserta1->id, 'quiz_id' => $quiz1->id, 'attempt_number' => 1],
            [
                'started_at' => now()->subHours(2),
                'submitted_at' => now()->subHour(),
                'score' => 100,
                'max_score' => 100,
                'percentage' => 100.0,
                'passed' => true,
                'status' => 'submitted',
            ]
        );

        // Issue sample official certificate for Peserta 1
        $certCode = 'BKS-SPBE20260901';
        $certNumber = 'CERT/BKS/' . date('Y') . '/' . date('m') . '/00001';

        Certificate::firstOrCreate(
            ['user_id' => $peserta1->id, 'course_id' => $course1->id],
            [
                'certificate_number' => $certNumber,
                'certificate_code' => $certCode,
                'issued_at' => now()->subHour(),
                'certificate_url' => url('/certificates/' . $certCode),
                'verification_url' => url('/certificates/verify/' . $certCode),
            ]
        );

        // Seed Enrollment in progress for Peserta 2
        Enrollment::firstOrCreate(
            ['user_id' => $peserta2->id, 'course_id' => $course1->id],
            [
                'enrolled_at' => now()->subDay(),
                'started_at' => now()->subDay(),
                'status' => 'active',
            ]
        );

        LessonProgress::updateOrCreate(
            ['user_id' => $peserta2->id, 'lesson_id' => $lesson1->id],
            [
                'status' => 'completed',
                'progress_percentage' => 100.0,
                'started_at' => now()->subDay(),
                'completed_at' => now()->subDay(),
            ]
        );

        CourseProgress::updateOrCreate(
            ['user_id' => $peserta2->id, 'course_id' => $course1->id],
            [
                'total_lessons' => 3,
                'completed_lessons' => 1,
                'progress_percentage' => 33.33,
                'started_at' => now()->subDay(),
                'last_accessed_at' => now(),
                'status' => 'in_progress',
            ]
        );

        // Initial Notification
        Notification::firstOrCreate(
            ['user_id' => $peserta1->id, 'title' => 'Selamat Datang di E-Belajar Bengkalis'],
            [
                'message' => 'Akun Anda telah aktif. Silakan pilih kursus pembelajaran mandiri Anda.',
                'type' => 'success',
            ]
        );

        // Initial Activity Logs
        ActivityLogService::log(
            action: 'system_initialized',
            description: 'Sistem E-Belajar Kabupaten Bengkalis berhasil diinisialisasi dengan data master.',
            user: $superadmin
        );

        $this->call(FaqSeeder::class);
    }
}
