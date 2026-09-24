<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FirewallMiddleware
{
    /**
     * Known vulnerability scanning tools & automated exploit bot signatures.
     *
     * @var array<string>
     */
    protected array $scannerSignatures = [
        'sqlmap',
        'nikto',
        'acunetix',
        'nessus',
        'openvas',
        'w3af',
        'dirbuster',
        'gobuster',
        'masscan',
        'zgrab',
        'nmap',
        'havij',
        'arachni',
        'whatweb',
        'burpcollaborator',
    ];

    /**
     * Common sensitive files and probe paths targeted by scanning tools.
     *
     * @var array<string>
     */
    protected array $blockedPaths = [
        '.env',
        '.git',
        '.svn',
        '.aws',
        'composer.json',
        'composer.lock',
        'package.json',
        'web.config',
        'phpinfo',
        'wp-config',
        'wp-login.php',
        'wp-admin',
        'eval-stdin.php',
        'cgi-bin',
    ];

    /**
     * Regex patterns that unambiguously indicate SQL injection attacks.
     *
     * @var array<string>
     */
    protected array $sqliPatterns = [
        // UNION SELECT attacks
        '/\bunion\s+(?:all\s+)?select\b/i',
        // Boolean-based and tautology SQLi, e.g. ' OR '1'='1, ' OR 1=1, " OR 1=1--
        '/(?:\'|%27|")\s*(?:or|and)\s+(?:\'|%27|")?\d+(?:\'|%27|")?\s*=\s*(?:\'|%27|")?\d+/i',
        '/(?:\'|%27|")\s*(?:or|and)\s+[\w\s]+\s*=\s*[\w\s]+--/i',
        // Time-based blind SQLi: sleep(), benchmark(), waitfor delay
        '/\b(?:sleep\s*\(|benchmark\s*\(|waitfor\s+delay\b)/i',
        // Schema inspection & XML extraction attacks
        '/\b(?:information_schema|extractvalue\s*\(|updatexml\s*\(|load_file\s*\(|into\s+(?:dump|out)file\b)/i',
        // Inline comments and stacked query destructive keywords
        '/(?:;|--|#|\/\*)\s*(?:drop|alter|truncate|delete|insert|update)\s+/i',
    ];

    /**
     * Dangerous file extensions that can execute server-side or client-side scripts.
     *
     * @var array<string>
     */
    protected array $blockedExtensions = [
        // PHP scripts and variants
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'phps', 'pht', 'inc',
        // Shell scripts & OS commands
        'sh', 'bash', 'zsh', 'csh', 'ksh', 'bat', 'cmd', 'ps1', 'psm1', 'psd1',
        'vbs', 'vbe', 'wsf', 'wsc',
        // Common scripting & interpreted languages
        'py', 'pyc', 'pyw', 'rb', 'pl', 'cgi',
        // Active server pages & Java server scripts
        'asp', 'aspx', 'axd', 'asmx', 'ashx', 'cer', 'asa', 'asax',
        'jsp', 'jspx', 'jsw', 'jsv', 'jspf',
        // Binaries / Executables
        'exe', 'dll', 'com', 'scr', 'msi', 'bin', 'elf',
        // Client scripts & markup capable of script execution (Stored XSS)
        'js', 'mjs', 'htm', 'html', 'shtml', 'shtm', 'xhtml', 'svg', 'xml',
        // Server configuration & sensitive system files
        'htaccess', 'htpasswd', 'ini', 'conf', 'config', 'env',
    ];

    /**
     * MIME types that indicate executable script content.
     *
     * @var array<string>
     */
    protected array $blockedMimeTypes = [
        'text/php',
        'application/x-php',
        'application/php',
        'text/x-php',
        'application/x-httpd-php',
        'application/x-httpd-php-source',
        'application/x-sh',
        'application/x-shellscript',
        'text/x-shellscript',
        'application/x-executable',
        'application/x-msdownload',
        'application/exe',
        'application/x-msdos-program',
        'text/html',
        'text/javascript',
        'application/javascript',
        'application/x-javascript',
        'image/svg+xml',
        'text/xml',
        'application/xml',
        'application/x-python',
        'text/x-python',
        'application/x-perl',
        'text/x-perl',
        'application/x-ruby',
    ];

    /**
     * Handle incoming request against scanning tools, SQL injection vectors, and dangerous uploads.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Inspect User-Agent for known scanning tools
        $userAgent = strtolower($request->header('User-Agent', ''));
        if (!empty($userAgent)) {
            foreach ($this->scannerSignatures as $scanner) {
                if (str_contains($userAgent, $scanner)) {
                    Log::warning('Security Alert: Automated vulnerability scanner blocked.', [
                        'ip' => $request->ip(),
                        'scanner' => $scanner,
                        'user_agent' => $request->header('User-Agent'),
                        'url' => $request->fullUrl(),
                    ]);

                    return response()->json([
                        'error' => 'Akses ditolak: Alat pemindai keamanan otomatis terdeteksi.',
                    ], 403);
                }
            }
        }

        // 2. Block sensitive probe paths frequently probed by scanners
        $path = strtolower(trim($request->path(), '/'));
        foreach ($this->blockedPaths as $blocked) {
            if ($path === $blocked || str_starts_with($path, $blocked . '/') || str_contains($path, '/' . $blocked)) {
                Log::warning('Security Alert: Probe to sensitive path blocked.', [
                    'ip' => $request->ip(),
                    'path' => $path,
                    'url' => $request->fullUrl(),
                ]);

                return response()->json([
                    'error' => 'Halaman atau berkas tidak ditemukan.',
                ], 404);
            }
        }

        // 3. Inspect request parameters, query string, and inputs for SQL injection patterns
        if ($this->hasMaliciousPayload($request)) {
            Log::warning('Security Alert: Malicious SQL injection payload blocked.', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'error' => 'Permintaan tidak valid: Muatan berbahaya terdeteksi.',
            ], 400);
        }

        // 4. Inspect uploaded files for executable scripts and malicious extensions
        if ($this->hasMaliciousFileUpload($request)) {
            Log::warning('Security Alert: Malicious file upload attempt blocked.', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'error' => 'Unggahan berkas tidak diizinkan: Berkas yang dapat menjalankan skrip dilarang.',
            ], 400);
        }

        return $next($request);
    }

    /**
     * Inspect all uploaded files for dangerous extensions, double extensions, and script content.
     */
    protected function hasMaliciousFileUpload(Request $request): bool
    {
        $files = $request->allFiles();
        if (empty($files)) {
            return false;
        }

        return $this->scanUploadedFiles($files);
    }

    /**
     * Recursively inspect uploaded file array.
     */
    protected function scanUploadedFiles(array $files): bool
    {
        foreach ($files as $file) {
            if (is_array($file)) {
                if ($this->scanUploadedFiles($file)) {
                    return true;
                }
            } elseif ($file instanceof UploadedFile) {
                if ($this->isFileMalicious($file)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if an individual uploaded file is malicious or executable.
     */
    protected function isFileMalicious(UploadedFile $file): bool
    {
        $clientOriginalName = $file->getClientOriginalName();
        $normalizedName = strtolower($clientOriginalName);

        // 1. Check for null byte injection or directory traversal in filename
        if (str_contains($clientOriginalName, "\0") || str_contains($clientOriginalName, '%00') ||
            str_contains($clientOriginalName, '../') || str_contains($clientOriginalName, '..\\')) {
            return true;
        }

        // 2. Check all extension segments (e.g. avatar.php.png or shell.phtml)
        $parts = explode('.', $normalizedName);
        if (count($parts) > 1) {
            array_shift($parts); // Remove base name
            foreach ($parts as $part) {
                $cleanPart = trim($part, " \t\n\r\0\x0B.");
                if (in_array($cleanPart, $this->blockedExtensions, true)) {
                    return true;
                }
            }
        }

        // 3. Check client extension and guessed extension
        $clientExtension = strtolower($file->getClientOriginalExtension());
        $guessedExtension = strtolower($file->guessExtension() ?? '');

        if (in_array($clientExtension, $this->blockedExtensions, true) ||
            in_array($guessedExtension, $this->blockedExtensions, true)) {
            return true;
        }

        // 4. Check client MIME type and detected MIME type
        $clientMime = strtolower($file->getClientMimeType() ?? '');
        $detectedMime = strtolower($file->getMimeType() ?? '');

        foreach ($this->blockedMimeTypes as $blockedMime) {
            if ($clientMime === $blockedMime || $detectedMime === $blockedMime) {
                return true;
            }
        }

        // 5. Inspect file content for PHP opening tags and executable scripts
        $realPath = $file->getRealPath();
        if ($realPath && file_exists($realPath) && is_readable($realPath)) {
            $content = @file_get_contents($realPath, false, null, 0, 8192);
            if ($content !== false && $content !== '') {
                // Check PHP tags: <?php, <?=, <script language="php"
                if (preg_match('/<\?(?:php|=)/i', $content) || stripos($content, '<script language="php"') !== false) {
                    return true;
                }

                // Check for Unix shebang executable scripts
                if (str_starts_with($content, '#!/bin/') || str_starts_with($content, '#!/usr/bin/')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recursively scan request inputs and raw query string for SQLi payloads.
     */
    protected function hasMaliciousPayload(Request $request): bool
    {
        // Check raw query string first (to catch encoded payloads before parsing)
        $queryString = rawurldecode($request->getQueryString() ?? '');
        if ($this->stringMatchesSqli($queryString)) {
            return true;
        }

        // Check all input values (GET and POST parameters)
        $inputs = $request->except(['_token', 'password', 'password_confirmation']);
        return $this->scanArrayForSqli($inputs);
    }

    /**
     * Recursively inspect array values.
     */
    protected function scanArrayForSqli(array $data): bool
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                if ($this->scanArrayForSqli($value)) {
                    return true;
                }
            } elseif (is_string($value)) {
                if ($this->stringMatchesSqli($value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Match a single string value against SQL injection signatures.
     */
    protected function stringMatchesSqli(string $value): bool
    {
        if (empty($value) || strlen($value) < 4) {
            return false;
        }

        // Check path traversal
        if (str_contains($value, '../') || str_contains($value, '..\\')) {
            return true;
        }

        foreach ($this->sqliPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}
