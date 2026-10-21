<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test the homepage loads successfully with editorial branding and compliance notices.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Franchise Dekho');
        $response->assertSee('Pursue Verified Business Opportunities with Coordinated Support.');
        $response->assertSee('Non-Negotiable Regulatory & Advisory Notice', false);
    }
}
