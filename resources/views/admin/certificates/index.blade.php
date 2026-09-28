@extends('layouts.admin')

@section('title', 'Penandatangan Sertifikat')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penandatangan Sertifikat</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola nama, NIP, jabatan, dan gambar tanda tangan/stempel pejabat penerbit sertifikat kelulusan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.issued-certificates.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Kelola No. Sertifikat</span>
            </a>
            <a href="{{ route('admin.certificates.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-sm transition-all hover:-translate-y-0.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Penandatangan</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 gap-4 text-sm font-bold">
        <a href="{{ route('admin.certificates.index') }}" class="pb-3 px-1 border-b-2 border-emerald-700 text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <span>Pejabat Penandatangan</span>
        </a>
        <a href="{{ route('admin.issued-certificates.index') }}" class="pb-3 px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Daftar & Edit No. Sertifikat</span>
        </a>
    </div>

    <!-- Active Signer Live Preview Card -->
    @if($activeSigner)
    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Pratinjau Tampilan pada Sertifikat (Sedang Aktif)</h3>
            </div>
            <a href="{{ route('admin.certificates.edit', $activeSigner->id) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline">Ubah Data Ini &rarr;</a>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-emerald-100 shadow-sm max-w-md mx-auto text-center">
            <div class="text-xs text-slate-600 mb-1">
                Bengkalis, {{ now()->translatedFormat('d F Y') }}
            </div>
            <div class="text-[11px] font-bold text-slate-800">
                {{ $activeSigner->jabatan ?? 'Kepala Dinas Komunikasi, Informatika dan Statistik' }}
            </div>
            <div class="text-[11px] font-bold text-slate-800 mb-2">
                {{ $activeSigner->instansi ?? 'Kabupaten Bengkalis' }}
            </div>

            <!-- Overlapping Signature + Stamp on the Left behind the name -->
            <div class="relative inline-block text-left pt-6 pb-2 min-w-[260px] mx-auto">
                @if($activeSigner->signature_image)
                    <img src="{{ asset('storage/' . $activeSigner->signature_image) }}" 
                         alt="TTD & Stempel"
                         class="absolute -left-12 -top-4 w-32 h-24 object-contain pointer-events-none select-none z-0 mix-blend-multiply opacity-95">
                @endif
                <div class="relative z-10 pl-6">
                    <div class="font-bold text-sm text-slate-900 tracking-wide">
                        {{ $activeSigner->name }}
                    </div>
                    <div class="text-slate-600 font-mono text-xs mt-0.5">
                        NIP. {{ $activeSigner->nip }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Signers Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Daftar Pejabat Penandatangan ({{ $signers->total() }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-6">Pejabat & NIP</th>
                        <th class="py-3.5 px-6">Jabatan & Instansi</th>
                        <th class="py-3.5 px-6 text-center">Gambar TTD / Stempel</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($signers as $signer)
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $signer->is_active ? 'bg-emerald-50/20' : '' }}">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $signer->name }}</div>
                                <div class="text-xs font-mono text-slate-500 mt-0.5">NIP. {{ $signer->nip }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                <div class="font-semibold">{{ $signer->jabatan }}</div>
                                <div class="text-slate-500">{{ $signer->instansi }}</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($signer->signature_image)
                                    <div class="inline-block p-1 bg-white border border-slate-200 rounded-xl shadow-xs">
                                        <img src="{{ asset('storage/' . $signer->signature_image) }}" alt="TTD" class="h-12 w-20 object-contain mx-auto">
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($signer->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif Digunakan
                                    </span>
                                @else
                                    <form action="{{ route('admin.certificates.set-active', $signer->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-colors">
                                            Jadikan Aktif
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.certificates.edit', $signer->id) }}" class="p-2 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <form action="{{ route('admin.certificates.destroy', $signer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penandatangan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada data penandatangan. Silakan tambahkan data penandatangan baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($signers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $signers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
