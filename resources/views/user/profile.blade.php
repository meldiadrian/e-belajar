@extends('layouts.app')

@section('title', 'Edit Profil Pengguna - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-700">Beranda</a>
        <span>/</span>
        <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
        <span>/</span>
        <span class="font-bold text-slate-800">Edit Profil</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Profile Card -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="relative">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-400 shadow-md">
                @else
                    <div class="w-20 h-20 rounded-2xl gradient-bengkalis flex items-center justify-center text-white text-2xl font-black shadow-md border-2 border-emerald-400">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="text-center sm:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <h1 class="text-xl sm:text-2xl font-black text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/30 text-emerald-200 border border-emerald-400/30">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 font-mono">{{ $user->email }}</p>
                <p class="text-xs text-emerald-300 mt-1 font-semibold">{{ $user->institution ?? 'Masyarakat Umum / Aparatur' }}</p>
            </div>
        </div>

        <!-- Form Update Profile -->
        <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">Informasi Pribadi</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('name')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('email')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="institution" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instansi / Unit Kerja</label>
                        <input type="text" name="institution" id="institution" value="{{ old('institution', $user->institution) }}" placeholder="Contoh: Disdik Bengkalis / Bappeda" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('institution')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('phone')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="avatar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Perbarui Foto Profil</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-slate-400 mt-1">Format gambar: JPG, PNG, maks 2MB.</p>
                    @error('avatar')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">Ubah Kata Sandi (Opsional)</h3>
                <p class="text-xs text-slate-500">Kosongkan kolom di bawah jika Anda tidak ingin mengganti kata sandi.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    &larr; Batal & Kembali ke Dashboard
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
