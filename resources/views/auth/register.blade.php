@extends('layouts.app')

@section('title', 'Daftar Akun Baru - E-Belajar Kabupaten Bengkalis')

@push('styles')
    <style>
        footer {
            display: none !important;
        }
    </style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-4 sm:py-6">
    <div class="w-full max-w-lg bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-lg space-y-3.5">
        <div class="text-center">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                class="w-10 h-10 mx-auto object-contain mb-1.5 drop-shadow-xs">
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Peserta</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Lengkapi formulir untuk memulai pembelajaran digital gratis</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-2.5">
            @csrf

            <div>
                <label for="name" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap Anda"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                @error('name')
                    <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                @error('email')
                    <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label for="institution" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Instansi / Unit Kerja</label>
                    <input type="text" name="institution" id="institution" value="{{ old('institution') }}" placeholder="Contoh: Disdik Bengkalis"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                </div>
                <div>
                    <label for="phone" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor WhatsApp/HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label for="password" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kata Sandi</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                    @error('password')
                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Ulangi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors shadow-sm hover:shadow text-xs sm:text-sm">
                Daftar Sebagai Peserta
            </button>
        </form>

        <div class="text-center text-[11px] text-slate-500 pt-1 border-t border-slate-100">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:underline">Masuk ke Portal</a>
        </div>
    </div>
</div>
@endsection
