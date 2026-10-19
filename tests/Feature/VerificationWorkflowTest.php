<?php

namespace Tests\Feature;

use App\Models\Opportunity;
use App\Models\User;
use App\Models\VerificationCase;
use App\Services\VerificationService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected VerificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VerificationService();
    }

    /**
     * Test case creation initializes standard 8 audit checks and audit log.
     */
    public function test_create_case_initializes_standard_checks(): void
    {
        $opportunity = Opportunity::factory()->draft()->create();
        $reviewer = User::factory()->create();

        $case = $this->service->createCase($opportunity, $reviewer, 'Initial case review.');

        $this->assertInstanceOf(VerificationCase::class, $case);
        $this->assertEquals('under_review', $case->status);
        $this->assertCount(8, $case->checks);
        $this->assertFalse($case->areMandatoryChecksPassed());

        // Verify audit event
        $this->assertCount(1, $case->auditEvents);
        $this->assertEquals('case_created', $case->auditEvents->first()->event);
    }

    /**
     * Test that publication is strictly blocked if mandatory checks are incomplete.
     */
    public function test_cannot_publish_when_mandatory_checks_are_incomplete(): void
    {
        $opportunity = Opportunity::factory()->draft()->create();
        $reviewer = User::factory()->create();
        $case = $this->service->createCase($opportunity, $reviewer);

        // Pass only 1 check out of 8
        $firstCheck = $case->checks->first();
        $this->service->passCheck($firstCheck, $reviewer, 'MCA verification confirmed on mca.gov.in');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot approve verification');

        $this->service->approveAndPublish($case, $reviewer);
    }

    /**
     * Test full approval workflow: passing all checks publishes opportunity and sets valid-until date.
     */
    public function test_passing_all_mandatory_checks_approves_and_publishes_opportunity(): void
    {
        $opportunity = Opportunity::factory()->draft()->create();
        $reviewer = User::factory()->create();
        $case = $this->service->createCase($opportunity, $reviewer);

        // Pass all 8 mandatory checks with provenance
        foreach ($case->checks as $check) {
            $this->service->passCheck(
                $check,
                $reviewer,
                "Verified check [{$check->check_type}] against verified source document."
            );
        }

        $this->assertTrue($case->areMandatoryChecksPassed());

        // Now approve and publish
        $success = $this->service->approveAndPublish($case, $reviewer, 180, 'All audit criteria verified.');

        $this->assertTrue($success);

        $case->refresh();
        $opportunity->refresh();

        $this->assertEquals('approved', $case->status);
        $this->assertEquals('published', $opportunity->publication_status);
        $this->assertNotNull($opportunity->last_verified_at);
        $this->assertNotNull($opportunity->verification_valid_until);
        $this->assertTrue($opportunity->isVerified());
        $this->assertEquals('verified', $opportunity->company->status);

        // Verify opportunity is returned in published query scope
        $published = Opportunity::published()->get();
        $this->assertTrue($published->contains($opportunity));
    }

    /**
     * Test suspending an opportunity immediately removes it from published query scope and logs audit event.
     */
    public function test_suspend_opportunity_hides_it_from_catalog_and_logs_audit(): void
    {
        $opportunity = Opportunity::factory()->draft()->create();
        $reviewer = User::factory()->create();
        $case = $this->service->createCase($opportunity, $reviewer);

        foreach ($case->checks as $check) {
            $this->service->passCheck($check, $reviewer, 'Verified');
        }
        $this->service->approveAndPublish($case, $reviewer);

        // Confirm it is published
        $this->assertTrue(Opportunity::published()->where('id', $opportunity->id)->exists());

        // Suspend due to territory dispute
        $this->service->suspendOpportunity(
            $opportunity,
            $reviewer,
            'Territory availability dispute raised by master franchisor.'
        );

        $opportunity->refresh();
        $this->assertEquals('suspended', $opportunity->publication_status);

        // Must NO LONGER appear in published scope
        $this->assertFalse(Opportunity::published()->where('id', $opportunity->id)->exists());

        // Verify audit log
        $case->refresh();
        $suspendedEvent = $case->auditEvents->where('event', 'opportunity_suspended')->first();
        $this->assertNotNull($suspendedEvent);
        $this->assertEquals('Territory availability dispute raised by master franchisor.', $suspendedEvent->reason);
    }
}
