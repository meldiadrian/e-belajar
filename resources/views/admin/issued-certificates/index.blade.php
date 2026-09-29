@extends('layouts.admin')

@section('title', 'Kelola Nomor Sertifikat')
@section('page_title', 'Kelola Nomor Sertifikat')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Nomor Sertifikat</h1>
            <p class="text-sm text-slate-500 mt-1">Lihat dan ubah nomor sertifikat resmi yang telah diterbitkan kepada peserta pelatihan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.certificates.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Pengaturan Penandatangan</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 gap-4 text-sm font-bold">
        <a href="{{ route('admin.issued-certificates.index') }}" class="pb-3 px-1 border-b-2 border-[#4E9CE8] text-[#4D52B4] flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Daftar & Edit No. Sertifikat ({{ $certificates->total() }})</span>
        </a>
        <a href="{{ route('admin.certificates.index') }}" class="pb-3 px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span>Pejabat Penandatangan</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.issued-certificates.index') }}" method="GET" class="w-full sm:max-w-md flex items-center gap-2">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Sertifikat, Nama Peserta, Kursus..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#4D52B4] hover:bg-[#4E9CE8] text-white rounded-xl text-xs font-bold transition-colors shrink-0">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.issued-certificates.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-semibold shrink-0">
                    Reset
                </a>
            @endif
        </form>
        <span class="text-xs text-slate-500 font-medium">Menampilkan {{ $certificates->firstItem() ?? 0 }} - {{ $certificates->lastItem() ?? 0 }} dari {{ $certificates->total() }} sertifikat</span>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-6">Nomor Sertifikat</th>
                        <th class="py-3.5 px-6">Peserta Penerima</th>
                        <th class="py-3.5 px-6">Kursus Pelatihan</th>
                        <th class="py-3.5 px-6">Tanggal Terbit</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($certificates as $cert)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-mono font-bold text-slate-900 tracking-wide text-xs sm:text-sm text-[#4D52B4] bg-[#4E9CE8]/10 px-2.5 py-1 rounded-lg inline-block border border-[#4E9CE8]/30">
                                    {{ $cert->certificate_number }}
                                </div>
                                <div class="text-[11px] font-mono text-slate-400 mt-1">Kode: {{ $cert->certificate_code }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $cert->user?->name ?? 'User Telah Dihapus' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">
                                    {{ $cert->user?->nip ? 'NIP. ' . $cert->user->nip : $cert->user?->email }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                <div class="font-semibold text-slate-900">{{ $cert->course?->title ?? '-' }}</div>
                                <div class="text-slate-500 mt-0.5">{{ $cert->course?->category?->name ?? 'Umum' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <div>{{ $cert->issued_at ? $cert->issued_at->translatedFormat('d F Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $cert->issued_at ? $cert->issued_at->format('H:i') . ' WIB' : '' }}</div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.issued-certificates.edit', $cert->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-[#4E9CE8]/10 text-slate-700 hover:text-[#4D52B4] rounded-lg text-xs font-bold transition-colors border border-slate-200 hover:border-[#4E9CE8]/30" title="Ubah Nomor Sertifikat">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Ubah</span>
                                    </a>

                                    <a href="{{ route('certificates.show', $cert->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-[#4D52B4] hover:bg-[#4E9CE8]/10 rounded-lg transition-colors" title="Lihat Sertifikat Asli">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>

                                    <form action="{{ route('admin.issued-certificates.destroy', $cert->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sertifikat ini? Peserta tidak akan dapat melihat sertifikat ini lagi.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Sertifikat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-medium text-slate-600">Belum ada sertifikat yang diterbitkan.</p>
                                    <p class="text-[11px] text-slate-400">Sertifikat otomatis tercatat di sini saat peserta menyelesaikan kursus dan lulus kuis.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
