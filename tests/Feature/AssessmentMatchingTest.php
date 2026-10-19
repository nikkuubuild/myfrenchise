<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Opportunity;
use App\Models\OpportunityLocation;
use App\Models\OpportunityRequirement;
use App\Services\AssessmentMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentMatchingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test assessment index page loads with active sectors.
     */
    public function test_assessment_index_renders_with_sectors(): void
    {
        $category = Category::factory()->create([
            'name' => 'Automotive Care',
            'slug' => 'automotive-care',
            'is_active' => true,
        ]);

        $response = $this->get(route('assessment.index'));

        $response->assertStatus(200);
        $response->assertSee('Find Your Opportunity Compatibility Match');
        $response->assertSee('Automotive Care');
    }

    /**
     * Test validation rules for assessment submission.
     */
    public function test_assessment_validation_fails_with_missing_mandatory_inputs(): void
    {
        $response = $this->post(route('assessment.match'), []);

        $response->assertSessionHasErrors(['budget_range', 'state']);
    }

    /**
     * Test matching engine strictly excludes draft or unverified opportunities.
     */
    public function test_matching_engine_strictly_excludes_unverified_opportunities(): void
    {
        $category = Category::factory()->create();

        // Draft opportunity with valid budget & location
        $draftOpp = Opportunity::factory()->draft()->create([
            'category_id' => $category->id,
            'title' => 'Unverified Burger Joint',
            'min_investment' => 400000,
            'max_investment' => 800000,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $draftOpp->id,
            'state_code' => 'DL',
            'status' => 'available',
        ]);

        $service = app(AssessmentMatchingService::class);
        $results = $service->match([
            'budget_range' => '5l-15l',
            'state' => 'DL',
        ]);

        $this->assertCount(0, $results);
    }

    /**
     * Test matching engine excludes opportunities exceeding budget ceiling.
     */
    public function test_matching_engine_excludes_opportunities_exceeding_budget(): void
    {
        $category = Category::factory()->create();

        $expensiveOpp = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Luxury Fine Dine',
            'min_investment' => 4000000, // 40L
            'max_investment' => 6000000,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $expensiveOpp->id,
            'state_code' => 'MH',
            'status' => 'available',
        ]);

        $service = app(AssessmentMatchingService::class);
        $results = $service->match([
            'budget_range' => '5l-15l', // Max 15L
            'state' => 'MH',
        ]);

        $this->assertCount(0, $results);
    }

    /**
     * Test matching engine excludes opportunities with no territory availability in user state.
     */
    public function test_matching_engine_excludes_opportunities_outside_target_state(): void
    {
        $category = Category::factory()->create();

        $opp = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'South India Cafe',
            'min_investment' => 600000,
            'max_investment' => 1000000,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $opp->id,
            'state_code' => 'KA', // Karnataka only
            'status' => 'available',
        ]);

        $service = app(AssessmentMatchingService::class);
        $results = $service->match([
            'budget_range' => '5l-15l',
            'state' => 'GJ', // Gujarat queried
        ]);

        $this->assertCount(0, $results);
    }

    /**
     * Test matching engine ranks opportunities and provides explainable reasons and caveats.
     */
    public function test_matching_engine_ranks_and_explains_fit(): void
    {
        $fnb = Category::factory()->create(['name' => 'F&B', 'slug' => 'fnb']);
        $retail = Category::factory()->create(['name' => 'Retail', 'slug' => 'retail']);

        // Perfect match: F&B, within budget, exact space fit, matching state
        $perfectOpp = Opportunity::factory()->published()->create([
            'category_id' => $fnb->id,
            'title' => 'Chai Masters',
            'min_investment' => 500000,
            'max_investment' => 900000,
            'min_space_sqft' => 150,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $perfectOpp->id,
            'state_code' => 'MH',
            'state_name' => 'Maharashtra',
            'district' => 'Pune',
            'status' => 'available',
        ]);

        // Secondary match: Retail, fits budget, but different sector
        $retailOpp = Opportunity::factory()->published()->create([
            'category_id' => $retail->id,
            'title' => 'Stationery Hub',
            'min_investment' => 600000,
            'max_investment' => 1200000,
            'min_space_sqft' => 250,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $retailOpp->id,
            'state_code' => 'MH',
            'state_name' => 'Maharashtra',
            'district' => 'Mumbai',
            'status' => 'available',
        ]);

        $service = app(AssessmentMatchingService::class);
        $results = $service->match([
            'budget_range' => '5l-15l',
            'state' => 'MH',
            'district' => 'Pune',
            'carpet_area_sqft' => 200,
            'sector' => 'fnb',
            'experience' => 'first_time',
        ]);

        $this->assertCount(2, $results);
        $this->assertEquals('Chai Masters', $results->first()['opportunity']->title);
        $this->assertGreaterThan($results->last()['fit_score'], $results->first()['fit_score']);

        $firstMatch = $results->first();
        $this->assertNotEmpty($firstMatch['reasons']);
        $this->assertArrayHasKey('budget', $firstMatch['score_breakdown']);
        $this->assertArrayHasKey('space', $firstMatch['score_breakdown']);
        $this->assertArrayHasKey('sector', $firstMatch['score_breakdown']);
    }

    /**
     * Test HTTP POST /assessment/match displays matching results and regulatory disclaimers.
     */
    public function test_http_assessment_matching_submission_displays_results_and_disclaimer(): void
    {
        $category = Category::factory()->create(['name' => 'Beauty & Wellness', 'slug' => 'beauty']);

        $opp = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Aura Salon & Spa',
            'min_investment' => 800000,
            'max_investment' => 1400000,
            'min_space_sqft' => 300,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $opp->id,
            'state_code' => 'KA',
            'state_name' => 'Karnataka',
            'district' => 'Bengaluru',
            'status' => 'available',
        ]);

        $response = $this->post(route('assessment.match'), [
            'budget_range' => '5l-15l',
            'state' => 'KA',
            'district' => 'Bengaluru',
            'carpet_area_sqft' => 400,
            'sector' => 'beauty',
            'experience' => 'existing_retail',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Aura Salon & Spa');
        $response->assertSee('Fit Score');
        $response->assertSee('Verified Match Factors');
        $response->assertSee('Caveats &amp; Unknowns to Verify', false);
        $response->assertSee('The Fit Score is an objective compatibility index');
    }
}
