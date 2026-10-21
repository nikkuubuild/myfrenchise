<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityBaselineTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test enterprise security headers are enforced on all web responses.
     */
    public function test_security_headers_are_present_on_web_requests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Test that rate limiter for enquiry is registered and enforces limits.
     */
    public function test_enquiry_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('enquiry');
        $this->assertNotNull($limiter, 'Enquiry rate limiter should be registered.');

        $request = \Illuminate\Http\Request::create('/enquiry', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(5, $limit->maxAttempts);
    }

    /**
     * Test that rate limiter for authentication is registered and enforces limits.
     */
    public function test_auth_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('auth');
        $this->assertNotNull($limiter, 'Auth rate limiter should be registered.');

        $request = \Illuminate\Http\Request::create('/login', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(5, $limit->maxAttempts);
    }
}
