<?php

namespace App\Services;

class GoogleAuthenticatorService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random 16-character Base32 secret key.
     */
    public function generateSecretKey(int $length = 16): string
    {
        $validChars = self::BASE32_CHARS;
        $maxIndex = strlen($validChars) - 1;
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $validChars[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    /**
     * Verify a 6-digit TOTP code against a secret key with an optional window.
     * Window 1 checks 30 seconds before and 30 seconds after (total 90 seconds).
     */
    public function verifyKey(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->calculateCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate a 6-digit code for a given secret and time slice.
     */
    public function calculateCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);
        if ($secretKey === '') {
            return '000000';
        }

        // Pack time into 8-byte binary string (big-endian)
        $time = pack('N*', 0) . pack('N*', $timeSlice);

        // Generate HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);

        // Extract 31-bit integer
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        // Modulo 10^6 for 6 digits
        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 string to raw binary string.
     */
    public function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $b32));
        if ($b32 === '') {
            return '';
        }

        $binaryString = '';
        $chars = self::BASE32_CHARS;

        for ($i = 0; $i < strlen($b32); $i++) {
            $pos = strpos($chars, $b32[$i]);
            if ($pos !== false) {
                $binaryString .= sprintf('%05b', $pos);
            }
        }

        $bytes = '';
        $length = strlen($binaryString);
        for ($i = 0; $i + 8 <= $length; $i += 8) {
            $bytes .= chr(bindec(substr($binaryString, $i, 8)));
        }

        return $bytes;
    }

    /**
     * Get the otpauth:// URI for scanning with Google Authenticator or other TOTP apps.
     */
    public function getOtpAuthUri(string $company, string $holder, string $secret): string
    {
        $label = rawurlencode($company) . ':' . rawurlencode($holder);
        $issuer = rawurlencode($company);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Formats secret into 4-character chunks for human readability (e.g. ABCD EFGH IJKL MNOP).
     */
    public function formatSecret(string $secret): string
    {
        return trim(chunk_split(strtoupper($secret), 4, ' '));
    }
}
