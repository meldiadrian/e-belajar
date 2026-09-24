@extends('layouts.app')

@section('title', 'Verifikasi Keaslian Sertifikat - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Lookup Box -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-md mb-8">
        <div class="flex items-center gap-3 mb-2">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis" class="w-10 h-10 object-contain">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">Layanan Verifikasi Sertifikat Resmi</h1>
                <p class="text-xs text-slate-500">Pemerintah Kabupaten Bengkalis &bull; Badan Penyelenggara E-Belajar</p>
            </div>
        </div>

        <form action="" method="GET" onsubmit="event.preventDefault(); window.location.href='/certificates/verify/' + document.getElementById('searchCode').value.trim();" class="mt-6 flex flex-col sm:flex-row gap-3">
            <input type="text" id="searchCode" value="{{ $code !== 'SAMPLE' ? $code : '' }}" required placeholder="Contoh: BKS-ABCDEF123456" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-sm font-mono tracking-wider uppercase focus:border-emerald-600 focus:outline-hidden">
            <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition-colors">
                Periksa Dokumen
            </button>
        </form>
    </div>

    <!-- Verification Result -->
    @if($code !== 'SAMPLE')
        @if($isValid)
            <div class="bg-white rounded-3xl border-2 border-emerald-500 shadow-xl overflow-hidden">
                <!-- Status Banner -->
                <div class="bg-emerald-600 text-white p-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-7 h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold tracking-widest uppercase text-emerald-100">Status Keabsahan Dokumen</div>
                            <div class="text-xl font-black text-white">TERVERIFIKASI ASLI & RESMI</div>
                        </div>
                    </div>
                    <span class="hidden sm:inline-block px-3 py-1 bg-white/10 rounded-full text-xs font-mono font-bold">{{ $verificationData['certificate_code'] }}</span>
                </div>

                <!-- Verified Details Table -->
                <div class="p-6 sm:p-8 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Nomor Registrasi Sertifikat</span>
                            <span class="font-bold text-slate-900 font-mono text-base">{{ $verificationData['certificate_number'] }}</span>
                        </div>
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Kode Unik Verifikasi</span>
                            <span class="font-bold text-emerald-700 font-mono text-base">{{ $verificationData['certificate_code'] }}</span>
                        </div>
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Nama Penerima / Peserta</span>
                            <span class="font-bold text-slate-900 text-base">{{ $verificationData['recipient_name'] }}</span>
                        </div>
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Instansi / Asal</span>
                            <span class="font-semibold text-slate-800">{{ $verificationData['institution'] }}</span>
                        </div>
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Program / Kursus yang Diselesaikan</span>
                            <span class="font-bold text-slate-900">{{ $verificationData['course_title'] }}</span>
                        </div>
                        <div class="border-b border-slate-100 pb-3">
                            <span class="text-xs text-slate-400 block mb-1">Tanggal Kelulusan / Terbit</span>
                            <span class="font-semibold text-slate-800">{{ $verificationData['issued_at'] }}</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mt-6 text-xs text-slate-500 leading-relaxed">
                        Dokumen ini diterbitkan secara elektronik oleh Sistem Informasi E-Belajar Pemerintah Kabupaten Bengkalis dan diakui sebagai bukti penyelesaian pelatihan kompetensi yang sah.
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl border-2 border-red-400 shadow-lg p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Sertifikat Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">
                    Kode sertifikat <code class="font-mono font-bold text-red-600">{{ $code }}</code> tidak tercatat dalam basis data resmi Pemerintah Kabupaten Bengkalis. Mohon periksa kembali penulisan kode pada dokumen Anda.
                </p>
            </div>
        @endif
    @endif
</div>
@endsection
