<?php

namespace Tests\Unit;

use App\Services\GoogleAuthenticatorService;
use PHPUnit\Framework\TestCase;

class GoogleAuthenticatorServiceTest extends TestCase
{
    private GoogleAuthenticatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoogleAuthenticatorService();
    }

    public function test_it_generates_valid_16_char_base32_secret(): void
    {
        $secret = $this->service->generateSecretKey();
        $this->assertEquals(16, strlen($secret));
        $this->assertMatchesRegularExpression('/^[A-Z2-7]{16}$/', $secret);
    }

    public function test_rfc6238_test_vectors(): void
    {
        // Secret "12345678901234567890" in Base32 is "GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ"
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

        // RFC 6238 Appendix B test vectors for SHA1
        // T0 = 0, X = 30
        $this->assertEquals('287082', $this->service->calculateCode($secret, 1));
        $this->assertEquals('081804', $this->service->calculateCode($secret, 37037036));
        $this->assertEquals('050471', $this->service->calculateCode($secret, 37037037));
        $this->assertEquals('005924', $this->service->calculateCode($secret, 41152263));
        $this->assertEquals('279037', $this->service->calculateCode($secret, 66666666));
    }

    public function test_verification_with_current_timestamp(): void
    {
        $secret = $this->service->generateSecretKey();
        $currentTimeSlice = (int) floor(time() / 30);
        $code = $this->service->calculateCode($secret, $currentTimeSlice);

        $this->assertTrue($this->service->verifyKey($secret, $code));
        $this->assertFalse($this->service->verifyKey($secret, '000000' === $code ? '111111' : '000000'));
    }
}
