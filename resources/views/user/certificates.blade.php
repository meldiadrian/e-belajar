@extends('layouts.app')

@section('title', 'Sertifikat Kelulusan Saya - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
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
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Sertifikat Sah</span>
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
                        <span class="font-mono font-bold text-emerald-700">{{ $cert->certificate_code }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Tanggal Terbit:</span>
                        <span class="font-medium text-slate-800">{{ $cert->issued_at->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('certificates.show', $cert->id) }}" class="flex-1 py-2.5 text-center bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                        Buka & Cetak Sertifikat
                    </a>
                    <a href="{{ route('certificates.verify', $cert->certificate_code) }}" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Cek Verifikasi Publik
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white p-12 rounded-3xl border border-slate-200 text-center">
                <p class="text-xs text-slate-400">Belum ada sertifikat yang diterbitkan. Selesaikan pembelajaran kursus hingga 100% untuk memperoleh sertifikat resmi.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $certificates->links() }}
    </div>
</div>
@endsection
