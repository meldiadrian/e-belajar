<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Umum',
                'description' => 'Informasi umum tentang sistem E-Belajar Bengkalis dan kebijakan platform.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Akun & Pendaftaran',
                'description' => 'Panduan pendaftaran akun baru, login, dan profil pengguna.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kursus & Pembelajaran',
                'description' => 'Cara mengikuti pembelajaran, akses materi modul, dan video.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Sertifikat',
                'description' => 'Penerbitan, pengunduhan, dan verifikasi sertifikat kelulusan digital.',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $catData) {
            $cat = FaqCategory::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                $catData
            );
            $categoryMap[$catData['name']] = $cat->id;
        }

        $faqs = [
            [
                'question' => 'Apa itu portal E-Belajar Kabupaten Bengkalis?',
                'answer' => 'E-Belajar Kabupaten Bengkalis adalah portal pembelajaran mandiri (LMS) resmi yang disediakan oleh Pemerintah Kabupaten Bengkalis untuk meningkatkan kompetensi ASN, pegawai non-ASN, serta masyarakat umum di berbagai bidang keahlian.',
                'category' => 'Umum',
                'faq_category_id' => $categoryMap['Umum'] ?? null,
                'order' => 1,
                'is_published' => true,
            ],
            [
                'question' => 'Siapa saja yang dapat mengikuti kursus di portal ini?',
                'answer' => 'Seluruh masyarakat Kabupaten Bengkalis dan aparatur pemerintah dapat mendaftarkan diri secara gratis melalui menu Daftar Akun Baru untuk mengakses seluruh katalog kursus yang tersedia.',
                'category' => 'Akun & Pendaftaran',
                'faq_category_id' => $categoryMap['Akun & Pendaftaran'] ?? null,
                'order' => 2,
                'is_published' => true,
            ],
            [
                'question' => 'Apakah kursus di E-Belajar Bengkalis dipungut biaya?',
                'answer' => 'Tidak. Seluruh materi kursus, video pembelajaran, modul bacaan, latihan kuis, hingga penerbitan sertifikat digital disediakan secara gratis tanpa dipungut biaya apapun.',
                'category' => 'Umum',
                'faq_category_id' => $categoryMap['Umum'] ?? null,
                'order' => 3,
                'is_published' => true,
            ],
            [
                'question' => 'Bagaimana cara mendapatkan sertifikat kelulusan?',
                'answer' => 'Sertifikat digital resmi akan otomatis diterbitkan apabila Anda telah menyelesaikan seluruh modul pembelajaran hingga progress 100% dan berhasil lulus kuis penilaian dengan nilai di atas batas kelulusan (passing score).',
                'category' => 'Sertifikat',
                'faq_category_id' => $categoryMap['Sertifikat'] ?? null,
                'order' => 4,
                'is_published' => true,
            ],
            [
                'question' => 'Bagaimana cara memverifikasi keaslian sertifikat?',
                'answer' => 'Setiap sertifikat dilengkapi dengan kode verifikasi unik serta tautan verifikasi. Siapa pun dapat memverifikasi keabsahan dokumen melalui menu Verifikasi Sertifikat pada header portal.',
                'category' => 'Sertifikat',
                'faq_category_id' => $categoryMap['Sertifikat'] ?? null,
                'order' => 5,
                'is_published' => true,
            ],
            [
                'question' => 'Apa yang harus dilakukan jika saya lupa kata sandi akun?',
                'answer' => 'Anda dapat menghubungi administrator sistem atau pengelola LMS melalui layanan kontak yang tertera di bagian Pusat Bantuan pada footer portal.',
                'category' => 'Akun & Pendaftaran',
                'faq_category_id' => $categoryMap['Akun & Pendaftaran'] ?? null,
                'order' => 6,
                'is_published' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
