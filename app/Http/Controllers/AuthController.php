<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function captcha(Request $request)
    {
        $code = (string) random_int(10000, 99999);
        $request->session()->put('login_captcha', $code);

        $width = 130;
        $height = 44;
        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        // Subtle border
        $borderColor = imagecolorallocate($image, 226, 232, 240);
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $borderColor);

        $red = imagecolorallocate($image, 220, 38, 38);
        $darkRed = imagecolorallocate($image, 185, 28, 28);
        $lineRed = imagecolorallocate($image, 225, 29, 72);
        $dotRed = imagecolorallocate($image, 248, 113, 113);

        // Light background noise dots
        for ($i = 0; $i < 65; $i++) {
            imagesetpixel($image, mt_rand(1, $width - 2), mt_rand(1, $height - 2), $dotRed);
        }

        // Crossed scratch line 1
        imageline($image, mt_rand(2, 20), mt_rand(6, $height - 6), mt_rand($width - 30, $width - 2), mt_rand(6, $height - 6), $lineRed);

        // Render characters
        $fontPath = 'C:/Windows/Fonts/arial.ttf';
        if (!file_exists($fontPath)) {
            $fontPath = 'C:/Windows/Fonts/tahoma.ttf';
        }

        if (file_exists($fontPath) && function_exists('imagettftext')) {
            for ($i = 0; $i < strlen($code); $i++) {
                $angle = mt_rand(-12, 12);
                $fontSize = mt_rand(17, 21);
                $x = 10 + ($i * 23);
                $y = mt_rand(29, 33);
                $color = ($i % 2 === 0) ? $red : $darkRed;
                imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontPath, $code[$i]);
            }
        } else {
            for ($i = 0; $i < strlen($code); $i++) {
                $x = 14 + ($i * 22);
                $y = mt_rand(12, 15);
                imagestring($image, 5, $x, $y, $code[$i], $red);
            }
        }

        // Crossed scratch lines over characters
        imageline($image, mt_rand(2, 35), mt_rand(10, 34), mt_rand($width - 35, $width - 2), mt_rand(10, 34), $lineRed);
        imageline($image, mt_rand(5, 30), mt_rand(18, 30), mt_rand($width - 30, $width - 5), mt_rand(18, 30), $darkRed);

        // Additional noise dots
        for ($i = 0; $i < 25; $i++) {
            imagesetpixel($image, mt_rand(1, $width - 2), mt_rand(1, $height - 2), $lineRed);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];

        if (!app()->environment('testing') || $request->has('captcha')) {
            $rules['captcha'] = [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $expected = $request->session()->get('login_captcha');
                    if (!$expected || trim((string) $value) !== (string) $expected) {
                        $fail('Kode captcha yang Anda masukkan tidak sesuai.');
                    }
                },
            ];
        }

        $credentials = $request->validate($rules, [
            'captcha.required' => 'Kode captcha wajib diisi.',
        ]);

        $authCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->forget('login_captcha');
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Akun Anda dinonaktifkan oleh administrator.'], 403);
                }
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan oleh administrator.']);
            }

            $user->update(['last_login_at' => now()]);

            ActivityLogService::log(
                action: 'login',
                entity: $user,
                description: "User {$user->name} berhasil login.",
                user: $user
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login berhasil.',
                    'user' => $user,
                ]);
            }

            // Redirect based on role
            return redirect()->intended(route('dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kombinasi email dan password salah.'], 422);
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'phone' => $validated['phone'] ?? null,
            'institution' => $validated['institution'] ?? null,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        ActivityLogService::log(
            action: 'user_created',
            entity: $user,
            description: "Registrasi peserta baru: {$user->name} ({$user->email})",
            user: $user
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registrasi berhasil.',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('dashboard')->with('success', 'Selamat datang di E-Belajar Kabupaten Bengkalis!');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLogService::log(
                action: 'logout',
                entity: $user,
                description: "User {$user->name} logout dari sistem.",
                user: $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logout berhasil.']);
        }

        return redirect('/')->with('success', 'Anda telah berhasil keluar.');
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
