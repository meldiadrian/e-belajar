<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // Regular user can only edit their own profile
        if ($currentUser->isUser() && $currentUser->id !== $user->id) {
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

        if ($currentUser->isUser() && $currentUser->id !== $user->id) {
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

        // Regular user can only edit their own profile
        if ($currentUser->isUser() && $currentUser->id !== $user->id) {
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
            user: $user
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
