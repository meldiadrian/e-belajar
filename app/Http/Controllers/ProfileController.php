<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form for the given user ID.
     */
    public function edit($id, GoogleAuthenticatorService $twoFactorService)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Regular user and admin can only edit their own profile
        // Superadmin can edit their own profile or inspect/edit others
        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Akses ditolak. Anda hanya dapat mengubah profil Anda sendiri.');
        }

        $secret = null;
        $formattedSecret = null;
        $qrCodeUri = null;

        if (!$user->hasTwoFactorEnabled()) {
            $secret = session('profile_2fa_secret');
            if (!$secret || request()->has('new_2fa_secret')) {
                $secret = $twoFactorService->generateSecretKey();
                session(['profile_2fa_secret' => $secret]);
            }

            $company = 'E-Belajar Bengkalis';
            $holder = ($user->nip ? $user->nip . ' - ' : '') . $user->name . ' (' . ucfirst($user->role) . ')';
            $qrCodeUri = $twoFactorService->getOtpAuthUri($company, $holder, $secret);
            $formattedSecret = $twoFactorService->formatSecret($secret);
        }

        return view('user.profile', compact('user', 'secret', 'formattedSecret', 'qrCodeUri'));
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
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'nip.unique' => 'NIP ini sudah terdaftar untuk pengguna lain.',
            'avatar.image' => 'Berkas avatar harus berupa gambar.',
            'avatar.mimes' => 'Format avatar yang diizinkan: JPG, JPEG, PNG, WEBP.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $oldValues = $user->only(['name', 'email', 'nip', 'phone', 'institution', 'avatar']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => !empty($validated['nip']) ? trim($validated['nip']) : null,
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
            newValues: $user->fresh()->only(['name', 'email', 'nip', 'phone', 'institution', 'avatar']),
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

    /**
     * Enable Google Authenticator 2FA for the user.
     */
    public function enableTwoFactor(Request $request, $id, GoogleAuthenticatorService $twoFactorService)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'two_factor_secret' => ['required', 'string'],
            'two_factor_code' => ['required', 'string'],
        ], [
            'two_factor_code.required' => 'Kode 6-digit Google Authenticator wajib dimasukkan.',
            'two_factor_secret.required' => 'Kunci rahasia 2FA tidak ditemukan. Silakan muat ulang halaman.',
        ]);

        $secret = trim((string) $validated['two_factor_secret']);
        $code = trim((string) $validated['two_factor_code']);

        if (!$twoFactorService->verifyKey($secret, $code)) {
            throw ValidationException::withMessages([
                'two_factor_code' => 'Kode autentikator 6-digit yang Anda masukkan tidak sesuai atau telah kadaluarsa. Pastikan jam pada perangkat Anda akurat.',
            ]);
        }

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $request->session()->forget('profile_2fa_secret');

        ActivityLogService::log(
            action: '2fa_enabled',
            entity: $user,
            description: "Pengguna {$user->name} ({$user->role}) berhasil mengaktifkan Autentikasi Dua Faktor (Google Authenticator).",
            user: $currentUser
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Autentikasi Dua Faktor (Google Authenticator) berhasil diaktifkan.',
                'user' => $user->fresh(),
            ]);
        }

        return back()->with('success', 'Autentikasi Dua Faktor (Google Authenticator) berhasil diaktifkan untuk akun Anda!');
    }

    /**
     * Disable Google Authenticator 2FA for the user.
     */
    public function disableTwoFactor(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser->isSuperAdmin() && $currentUser->id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        // Jika user mematikan 2FA untuk dirinya sendiri, verifikasi kata sandi saat ini
        if ($currentUser->id === $user->id) {
            $request->validate([
                'current_password' => ['required', 'string'],
            ], [
                'current_password.required' => 'Kata sandi saat ini wajib diisi untuk menonaktifkan 2FA.',
            ]);

            if (!Hash::check($request->input('current_password'), $currentUser->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
                ]);
            }
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        ActivityLogService::log(
            action: '2fa_disabled',
            entity: $user,
            description: "Pengguna {$user->name} ({$user->role}) menonaktifkan Autentikasi Dua Faktor (Google Authenticator).",
            user: $currentUser
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Autentikasi Dua Faktor (Google Authenticator) telah dinonaktifkan.',
                'user' => $user->fresh(),
            ]);
        }

        return back()->with('success', 'Autentikasi Dua Faktor (Google Authenticator) telah dinonaktifkan.');
    }
}
