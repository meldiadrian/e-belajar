@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Kelola Pengguna & Peran (RBAC)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900">Basis Data Pengguna Sistem</h2>
            <p class="text-xs text-slate-500">Atur hak akses peran Superadmin, Admin Kursus, dan Peserta</p>
        </div>
        <button onclick="document.getElementById('modalAddUser').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Status Tabs -->
    <div class="flex border-b border-slate-200 text-xs font-bold gap-4">
        <a href="{{ route('superadmin.users.index', ['status' => 'active']) }}" class="pb-3 flex items-center gap-1.5 transition-colors {{ ($status ?? 'active') === 'active' ? 'text-emerald-700 border-b-2 border-emerald-600' : 'text-slate-500 hover:text-emerald-700 border-b-2 border-transparent' }}">
            <span>Pengguna Aktif</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $activeCount }}</span>
        </a>
        <a href="{{ route('superadmin.users.index', ['status' => 'trashed']) }}" class="pb-3 flex items-center gap-1.5 transition-colors {{ ($status ?? 'active') === 'trashed' ? 'text-amber-700 border-b-2 border-amber-600' : 'text-slate-500 hover:text-amber-700 border-b-2 border-transparent' }}">
            <span>Pengguna Terhapus (Sampah)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($status ?? 'active') === 'trashed' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $trashedCount }}</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('superadmin.users.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-4 items-center justify-between">
        <input type="hidden" name="status" value="{{ $status ?? 'active' }}">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau instansi..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:outline-hidden">
        </div>
        <div>
            <select name="role" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white" onchange="this.form.submit()">
                <option value="">Semua Peran</option>
                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Kursus</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Peserta</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800">
            Cari
        </button>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-100 font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Instansi & Kontak</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Statistik</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div>{{ $user->institution ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $user->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->trashed())
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 uppercase">
                                        {{ $user->role }}
                                    </span>
                                @else
                                    <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" onchange="this.form.submit()" class="px-2 py-1 rounded-lg text-xs font-bold border border-slate-200 {{ $user->isSuperAdmin() ? 'bg-amber-50 text-amber-900 font-bold' : ($user->isAdmin() ? 'bg-teal-50 text-teal-900' : 'bg-slate-100 text-slate-800') }}">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Peserta</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                        Terhapus
                                    </span>
                                @else
                                    <form action="{{ route('superadmin.users.toggle-active', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div>{{ $user->enrollments_count }} Kursus Diikuti</div>
                                <div class="text-[11px] text-amber-600 font-semibold">{{ $user->certificates_count }} Sertifikat</div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if($user->trashed())
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('superadmin.users.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[10px] transition-colors" title="Pulihkan Pengguna">
                                                Pulihkan
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.users.force-delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen akun {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-[10px] transition-colors" title="Hapus Permanen">
                                                Hapus Permanen
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1" title="Hapus Pengguna">
                                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data pengguna yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $users->links() }}
    </div>
</div>

<!-- Modal: Add User -->
<div id="modalAddUser" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 {{ $errors->any() ? '' : 'hidden' }}">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Tambah Akun Pengguna Baru</h3>
            <button type="button" onclick="document.getElementById('modalAddUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
        </div>
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 rounded-xl border {{ $errors->has('name') ? 'border-red-400 bg-red-50/30' : 'border-slate-300' }} text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('name')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 rounded-xl border {{ $errors->has('email') ? 'border-red-400 bg-red-50/30' : 'border-slate-300' }} text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('email')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-bold text-slate-700">Kata Sandi Awal *</label>
                    <span class="text-[10px] text-slate-400">Minimal 6 karakter</span>
                </div>
                <input type="password" name="password" required class="w-full px-3 py-2 rounded-xl border {{ $errors->has('password') ? 'border-red-400 bg-red-50/30' : 'border-slate-300' }} text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('password')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Peran (Role) *</label>
                    <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Peserta</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin Kursus</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Instansi</label>
                    <input type="text" name="institution" value="{{ old('institution') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddUser').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
