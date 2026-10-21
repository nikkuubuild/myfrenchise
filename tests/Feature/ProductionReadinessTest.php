<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 404 error page renders with editorial typography and without stack trace leaks.
     */
    public function test_404_error_page_renders_with_editorial_layout_and_no_debug_leak(): void
    {
        $response = $this->get('/this-route-definitely-does-not-exist-404-test');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Requested Resource Unavailable');
        $response->assertSee('Explore Verified Opportunities');
        $response->assertDontSee('Whoops\\Run');
        $response->assertDontSee('TraceAsString');
        $response->assertDontSee('SQLSTATE');
    }

    /**
     * Test 403 forbidden error view renders with security policy text.
     */
    public function test_403_error_view_renders_with_security_policy(): void
    {
        $view = $this->view('errors.403', [
            'exception' => new \Exception('Access restricted to assigned case coordinators under platform privacy policy.'),
        ]);

        $view->assertSee('403');
        $view->assertSee('Access Restricted by Security Policy');
        $view->assertSee('Access restricted to assigned case coordinators under platform privacy policy.');
        $view->assertSee('DPDPA 2023');
    }

    /**
     * Test 419 page expired error view renders.
     */
    public function test_419_error_view_renders_session_security_notice(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('419');
        $view->assertSee('Session Security Check');
        $view->assertSee('Cross-Site Request Forgery');
    }

    /**
     * Test 429 rate limit exceeded error view renders.
     */
    public function test_429_error_view_renders_rate_limit_notice(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('429');
        $view->assertSee('Rate Limit Exceeded');
        $view->assertSee('Request Frequency Throttled');
    }

    /**
     * Test 500 server error view renders zero-leakage masking notice.
     */
    public function test_500_error_view_renders_zero_leakage_notice(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Platform Incident Recorded');
        $view->assertSee('Zero-Leakage Production Masking');
        $view->assertSee('/health');
    }

    /**
     * Test 503 maintenance mode view renders.
     */
    public function test_503_maintenance_view_renders(): void
    {
        $view = $this->view('errors.503');

        $view->assertSee('503');
        $view->assertSee('Platform Upgrade in Progress');
        $view->assertSee('System Maintenance Underway');
    }

    /**
     * Test enterprise security headers are enforced across HTTP responses.
     */
    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
    }

    /**
     * Test private storage disk is physically isolated from public web document root.
     */
    public function test_private_storage_disk_is_isolated_from_public_path(): void
    {
        $localRoot = config('filesystems.disks.local.root');
        $publicPath = public_path();

        $this->assertFalse(str_starts_with($localRoot, $publicPath), 'Local storage disk root must not reside inside public web document root.');
        $this->assertTrue(
            str_contains($localRoot, 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'private') || str_ends_with($localRoot, 'private'),
            'Local storage disk must point to storage/app/private.'
        );
    }

    /**
     * Test production-check Artisan command runs and outputs diagnostic status.
     */
    public function test_production_check_artisan_command_executes_successfully(): void
    {
        $this->artisan('app:production-check')
            ->expectsOutputToContain('Starting Franchise Dekho Production Readiness Pre-flight Diagnostics...')
            ->expectsOutputToContain('PASS')
            ->assertExitCode(0);
    }

    /**
     * Test health check endpoint reports status and database connectivity.
     */
    public function test_health_check_endpoint_reports_status_and_database(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'app' => 'Franchise Dekho',
                'database' => 'connected',
            ]);
    }

    /**
     * Test public .htaccess file exists and contains security headers and rewrite rules.
     */
    public function test_public_htaccess_contains_hardening_rules(): void
    {
        $htaccessPath = public_path('.htaccess');

        $this->assertTrue(File::exists($htaccessPath), 'public/.htaccess must exist.');
        $content = File::get($htaccessPath);
        $this->assertStringContainsString('mod_headers.c', $content);
        $this->assertStringContainsString('X-Content-Type-Options', $content);
        $this->assertStringContainsString('RewriteEngine On', $content);
    }
}
