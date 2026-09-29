@extends('layouts.admin')

@section('title', 'Input Nomor Sertifikat')
@section('page_title', 'Input Nomor Sertifikat')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Card -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Input Nomor Sertifikat Baru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Input data sertifikat resmi peserta dengan penomoran indeks
                        otomatis.</p>
                </div>
                <a href="{{ route('admin.issued-certificates.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    &larr; Kembali ke Daftar
                </a>
            </div>

            <!-- Info Notification on Auto Increment -->
            <div class="bg-blue-50 border border-blue-200/80 rounded-2xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#4D52B4] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs text-slate-700 leading-relaxed">
                    <span class="font-bold text-[#4D52B4]">Penomoran Otomatis:</span>
                    Nomor index sertifikat berikutnya terisi otomatis per kursus pelatihan (<span id="auto-number-badge"
                        class="font-mono font-bold text-slate-900">{{ $nextCertificateNumber }}</span>). Untuk kursus pelatihan lainnya, Anda dapat menggunakan nomor sertifikat yang sama mulai dari awal (00001).
                </div>
            </div>

            <form action="{{ route('admin.issued-certificates.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- User / Recipient Selection -->
                <div>
                    <label for="user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Peserta Penerima <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        <option value="">-- Pilih Peserta --</option>

                        {{-- Special option for ALL users with role 'user' --}}
                        <option value="role_user" class="font-bold text-[#4D52B4]" {{ old('user_id', 'role_user') === 'role_user' ? 'selected' : '' }}>
                            ● Role User (Semua Peserta)
                        </option>

                    </select>
                    @error('user_id')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Course Selection -->
                <div>
                    <label for="course_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kursus Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        <option value="">-- Pilih Kursus Pelatihan --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Certificate Number -->
                <div>
                    <label for="certificate_number"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nomor Registrasi Sertifikat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="certificate_number" id="certificate_number"
                        value="{{ old('certificate_number', $nextCertificateNumber) }}" required
                        placeholder="Contoh: BKPP-PKA/2026/00001"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden font-mono tracking-wider font-bold text-slate-900">
                    <p class="text-[11px] text-slate-400 mt-1">Indeks berikutnya akan otomatis berlanjut mengikuti nomor
                        yang Anda simpan di sini.</p>
                    @error('certificate_number')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Issued Date -->
                <div>
                    <label for="issued_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Terbit Sertifikat
                    </label>
                    <input type="date" name="issued_at" id="issued_at" value="{{ old('issued_at', date('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden font-mono">
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal ini tertera pada tanda tangan pejabat sertifikat:
                        "Bengkalis, [Tanggal]".</p>
                    @error('issued_at')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit & Cancel -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.issued-certificates.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#4D52B4] hover:bg-[#4E9CE8] text-white font-bold text-xs shadow-md transition-colors">
                        Simpan Sertifikat
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseNextNumbers = @json($courseNextNumbers ?? []);
            const courseSelect = document.getElementById('course_id');
            const certInput = document.getElementById('certificate_number');
            const badge = document.getElementById('auto-number-badge');

            if (courseSelect && certInput) {
                courseSelect.addEventListener('change', function() {
                    const selectedId = this.value;
                    if (selectedId && courseNextNumbers[selectedId]) {
                        certInput.value = courseNextNumbers[selectedId];
                        if (badge) {
                            badge.textContent = courseNextNumbers[selectedId];
                        }
                    }
                });
            }
        });
    </script>
@endpush