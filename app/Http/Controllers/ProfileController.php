<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form for the given user ID.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Regular user and admin can only edit their own profile
        // Superadmin can edit their own profile or inspect/edit others
        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Akses ditolak. Anda hanya dapat mengubah profil Anda sendiri.');
        }

        return view('user.profile', compact('user'));
    }

    /**
     * Show user profile data for API.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            return response()->json([
                'message' => 'Akses ditolak. Anda hanya dapat melihat profil Anda sendiri.',
            ], 403);
        }

        return response()->json($user);
    }

    /**
     * Update the profile for the given user ID.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Regular user and admin can only edit their own profile
        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda hanya dapat mengubah profil Anda sendiri.',
                ], 403);
            }
            abort(403, 'Akses ditolak. Anda hanya dapat mengubah profil Anda sendiri.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'avatar.image' => 'Berkas avatar harus berupa gambar.',
            'avatar.mimes' => 'Format avatar yang diizinkan: JPG, JPEG, PNG, WEBP.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $oldValues = $user->only(['name', 'email', 'phone', 'institution', 'avatar']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'institution' => $validated['institution'] ?? null,
        ];

        // Process avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath;
        }

        // Process password update if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        ActivityLogService::log(
            action: 'profile_updated',
            entity: $user,
            description: "Pengguna {$user->name} memperbarui data profil.",
            oldValues: $oldValues,
            newValues: $user->fresh()->only(['name', 'email', 'phone', 'institution', 'avatar']),
            user: $currentUser
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'user' => $user->fresh(),
            ]);
        }

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
