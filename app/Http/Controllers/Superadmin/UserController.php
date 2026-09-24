<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $query = User::withCount(['courses', 'enrollments', 'certificates']);

        if ($status === 'trashed') {
            $query->onlyTrashed();
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $trashedCount = User::onlyTrashed()->count();
        $activeCount = User::count();

        return view('superadmin.users.index', compact('users', 'status', 'trashedCount', 'activeCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', Password::min(6)],
            'role' => ['required', 'in:user,admin,superadmin'],
            'institution' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar sebagai pengguna aktif.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna tidak valid.',
        ]);

        // If an account with this email was previously soft-deleted, purge it so the new user can be cleanly created
        $trashedUser = User::onlyTrashed()->where('email', $validated['email'])->first();
        if ($trashedUser) {
            $trashedUser->forceDelete();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'institution' => $validated['institution'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        ActivityLogService::log(
            action: 'user_created',
            entity: $user,
            description: "Superadmin membuat akun baru: {$user->name} ({$user->role})",
            newValues: $user->toArray()
        );

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan dengan role {$user->role}.");
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
            'role' => ['sometimes', 'in:user,admin,superadmin'],
            'institution' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap tidak boleh kosong.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $oldValues = $user->toArray();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLogService::log(
            action: 'user_updated',
            entity: $user,
            description: "Data pengguna {$user->name} diperbarui oleh Superadmin.",
            oldValues: $oldValues,
            newValues: $user->fresh()->toArray()
        );

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLogService::log(
            action: 'user_updated',
            entity: $user,
            description: "Status akun {$user->name} {$status}."
        );

        return back()->with('success', "Akun pengguna berhasil {$status}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();

        ActivityLogService::log(
            action: 'user_deleted',
            entity: $user,
            description: "Pengguna {$userName} dinonaktifkan/dihapus (soft delete)."
        );

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLogService::log(
            action: 'user_restored',
            entity: $user,
            description: "Akun pengguna {$user->name} ({$user->email}) dipulihkan kembali oleh Superadmin."
        );

        return back()->with('success', "Pengguna {$user->name} berhasil dipulihkan.");
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;
        $user->forceDelete();

        ActivityLogService::log(
            action: 'user_force_deleted',
            entity: null,
            description: "Pengguna {$userName} dihapus permanen dari database."
        );

        return back()->with('success', "Pengguna {$userName} berhasil dihapus permanen.");
    }
}
