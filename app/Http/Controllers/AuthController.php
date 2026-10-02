<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\GoogleAuthenticatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        $ip = request()->ip();
        if (RateLimiter::tooManyAttempts('login_ip:'.$ip, 10)) {
            abort(404);
        }

        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister(Request $request, GoogleAuthenticatorService $twoFactorService)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Generate or retrieve registration 2FA secret from session
        $secret = $request->session()->get('register_2fa_secret');
        if (!$secret || $request->has('new_secret')) {
            $secret = $twoFactorService->generateSecretKey();
            $request->session()->put('register_2fa_secret', $secret);
        }

        $company = 'E-Belajar Bengkalis';
        $holder = 'Peserta Baru';
        $qrCodeUri = $twoFactorService->getOtpAuthUri($company, $holder, $secret);
        $formattedSecret = $twoFactorService->formatSecret($secret);

        return view('auth.register', compact('secret', 'formattedSecret', 'qrCodeUri'));
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

    public function login(Request $request, GoogleAuthenticatorService $twoFactorService)
    {
        $ip = $request->ip();
        // Login dibatasi wajib menggunakan NIP, email tidak bisa lagi digunakan
        $nipInput = trim((string) $request->input('nip', ''));
        $ipKey = 'login_ip:'.$ip;
        $userKey = 'login_user:'.$nipInput.'|'.$ip;

        // Pembatasan brute force: jika melebihi 10 kali percobaan gagal, arahkan ke page 404
        if (RateLimiter::tooManyAttempts($ipKey, 10) || ($nipInput !== '' && RateLimiter::tooManyAttempts($userKey, 10))) {
            Log::warning('Security Alert: Upaya brute force login diblokir (404).', [
                'ip' => $ip,
                'identifier' => $nipInput,
                'user_agent' => $request->header('User-Agent'),
            ]);

            abort(404);
        }

        $rules = [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        if ((!app()->runningUnitTests() && !app()->environment('testing')) || $request->has('captcha')) {
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

        try {
            $credentials = $request->validate($rules, [
                'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
                'captcha.required' => 'Kode captcha wajib diisi.',
            ]);
        } catch (ValidationException $e) {
            RateLimiter::hit($ipKey, 900);
            if ($nipInput !== '') {
                RateLimiter::hit($userKey, 900);
            }

            throw $e;
        }

        $user = User::where('nip', $credentials['nip'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Jika akun pengguna telah mengaktifkan Google Authenticator (2FA)
            if ($user->hasTwoFactorEnabled()) {
                $twoFactorCode = trim((string) $request->input('two_factor_code', ''));

                if ($twoFactorCode === '') {
                    RateLimiter::hit($ipKey, 900);
                    if ($nipInput !== '') {
                        RateLimiter::hit($userKey, 900);
                    }

                    if ($request->wantsJson()) {
                        return response()->json(['message' => 'Akun Anda dilindungi Google Authenticator. Masukkan kode 6-digit.'], 422);
                    }

                    return back()->withInput($request->only('nip'))
                        ->withErrors([
                            'two_factor_code' => 'Akun Anda dilindungi Google Authenticator. Masukkan kode 6-digit dari aplikasi Authenticator Anda.',
                        ]);
                }

                if (!$twoFactorService->verifyKey($user->two_factor_secret, $twoFactorCode)) {
                    RateLimiter::hit($ipKey, 900);
                    if ($nipInput !== '') {
                        RateLimiter::hit($userKey, 900);
                    }

                    if ($request->wantsJson()) {
                        return response()->json(['message' => 'Kode Google Authenticator tidak sesuai atau telah kadaluarsa.'], 422);
                    }

                    return back()->withInput($request->only('nip'))
                        ->withErrors([
                            'two_factor_code' => 'Kode Google Authenticator tidak sesuai atau telah kadaluarsa.',
                        ]);
                }
            }

            if (!$user->is_active) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Akun Anda dinonaktifkan oleh administrator.'], 403);
                }
                return back()->withErrors(['nip' => 'Akun Anda dinonaktifkan oleh administrator.']);
            }

            Auth::login($user, $request->boolean('remember'));
            RateLimiter::clear($ipKey);
            if ($nipInput !== '') {
                RateLimiter::clear($userKey);
            }

            $request->session()->forget('login_captcha');
            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            ActivityLogService::log(
                action: 'login',
                entity: $user,
                description: "User {$user->name} berhasil login." . ($user->hasTwoFactorEnabled() ? ' (2FA Google Authenticator)' : ''),
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

        // Catat kegagalan login untuk pencegahan brute force
        RateLimiter::hit($ipKey, 900);
        if ($nipInput !== '') {
            RateLimiter::hit($userKey, 900);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kombinasi NIP dan kata sandi tidak sesuai.'], 422);
        }

        return back()->withInput($request->only('nip'))->withErrors([
            'nip' => 'NIP atau kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    public function register(Request $request, GoogleAuthenticatorService $twoFactorService)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nip' => ['required', 'string', 'max:30', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone' => ['nullable', 'string', 'max:30'],
            'institution' => ['nullable', 'string', 'max:255'],
        ];

        // 2FA wajib pada form pendaftaran web dan pengujian yang mengirim payload 2FA
        $requires2fa = $request->has('two_factor_code') || $request->has('two_factor_secret') || (!app()->runningUnitTests() && !app()->environment('testing'));

        if ($requires2fa) {
            $rules['two_factor_secret'] = ['required', 'string'];
            $rules['two_factor_code'] = ['required', 'string'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi untuk keperluan login portal.',
            'nip.unique' => 'NIP sudah terdaftar untuk pengguna lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'two_factor_code.required' => 'Kode 6-digit Google Authenticator wajib dimasukkan.',
            'two_factor_secret.required' => 'Kunci rahasia autentikator tidak ditemukan. Silakan muat ulang halaman pendaftaran.',
        ]);

        $twoFactorSecret = null;
        $twoFactorConfirmedAt = null;

        if ($requires2fa && !empty($validated['two_factor_secret'])) {
            $secret = trim((string) $validated['two_factor_secret']);
            $code = trim((string) $validated['two_factor_code']);

            if (!$twoFactorService->verifyKey($secret, $code)) {
                throw ValidationException::withMessages([
                    'two_factor_code' => 'Kode autentikator 6-digit yang Anda masukkan tidak sesuai atau telah kadaluarsa. Pastikan jam pada perangkat Anda tepat.',
                ]);
            }

            $twoFactorSecret = $secret;
            $twoFactorConfirmedAt = now();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => !empty($validated['nip']) ? trim($validated['nip']) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'phone' => $validated['phone'] ?? null,
            'institution' => $validated['institution'] ?? null,
            'is_active' => true,
            'two_factor_secret' => $twoFactorSecret,
            'two_factor_confirmed_at' => $twoFactorConfirmedAt,
        ]);

        $request->session()->forget('register_2fa_secret');
        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        ActivityLogService::log(
            action: 'user_created',
            entity: $user,
            description: "Registrasi peserta baru dengan Google Authenticator: {$user->name} ({$user->email})",
            user: $user
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registrasi berhasil.',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('dashboard')->with('success', 'Selamat datang di E-Belajar Kabupaten Bengkalis! Akun Anda aktif dan dilindungi Google Authenticator.');
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
