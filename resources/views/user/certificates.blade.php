@extends('layouts.admin')

@section('title', 'Sertifikat Kelulusan Saya - E-Belajar Kabupaten Bengkalis')
@section('page_title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-900">Sertifikat Kelulusan Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Dokumen resmi kelulusan program pembelajaran mandiri Pemerintah Kabupaten Bengkalis</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($certificates as $cert)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#4D52B4] bg-[#4E9CE8]/10 px-2 py-0.5 rounded-full">Sertifikat Sah</span>
                            <h3 class="text-base font-bold text-slate-900 mt-1">{{ $cert->course->title }}</h3>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl space-y-1 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Nomor Seri:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $cert->certificate_number }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Kode Verifikasi:</span>
                        <span class="font-mono font-bold text-[#4D52B4]">{{ $cert->certificate_code }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Tanggal Terbit:</span>
                        <span class="font-medium text-slate-800">{{ $cert->issued_at->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('certificates.show', $cert->id) }}" class="flex-1 py-2.5 text-center bg-[#4D52B4] hover:bg-[#4E9CE8] text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                        Buka & Cetak Sertifikat
                    </a>
                    <a href="{{ route('certificates.verify', $cert->certificate_code) }}" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Cek Verifikasi Publik
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 bg-white p-12 sm:p-20 rounded-3xl border border-slate-200 text-center flex flex-col items-center justify-center">
                <div class="w-32 h-32 mb-6 bg-[#70D6C5]/10 rounded-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-[#4D52B4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Sertifikat</h3>
                <p class="text-slate-500 text-sm max-w-md mx-auto mb-8">Anda belum memiliki sertifikat. Selesaikan pembelajaran kursus hingga 100% untuk meraih sertifikat resmi pertama Anda.</p>
                <a href="{{ route('user.my_courses') }}" class="inline-block px-8 py-3.5 bg-[#4D52B4] text-white rounded-xl text-sm font-bold hover:bg-[#4E9CE8] shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    Lanjutkan Pembelajaran Anda
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $certificates->links() }}
    </div>
</div>
@endsection
