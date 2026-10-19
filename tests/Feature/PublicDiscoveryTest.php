<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Models\OpportunityFinancialBreakdown;
use App\Models\OpportunityLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test homepage displays active sectors and verified opportunities.
     */
    public function test_homepage_loads_dynamic_categories_and_verified_opportunities(): void
    {
        $category = Category::factory()->create(['name' => 'Food & Beverage', 'is_active' => true]);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Chai Point Kiosk',
            'is_featured' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Food & Beverage');
        $response->assertSee('Chai Point Kiosk');
    }

    /**
     * Test opportunity catalog filtering by sector, budget, and state.
     */
    public function test_opportunity_catalog_filtering(): void
    {
        $fnbCat = Category::factory()->create(['name' => 'F&B', 'slug' => 'fnb']);
        $retailCat = Category::factory()->create(['name' => 'Retail', 'slug' => 'retail']);

        $fnbOpp = Opportunity::factory()->published()->create([
            'category_id' => $fnbCat->id,
            'title' => 'Tea Station Express',
            'min_investment' => 300000,
            'max_investment' => 500000,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $fnbOpp->id,
            'state_code' => 'UP',
        ]);

        $retailOpp = Opportunity::factory()->published()->create([
            'category_id' => $retailCat->id,
            'title' => 'Mega Mart Grocery',
            'min_investment' => 2000000,
            'max_investment' => 3500000,
        ]);
        OpportunityLocation::factory()->create([
            'opportunity_id' => $retailOpp->id,
            'state_code' => 'MH',
        ]);

        // Filter by sector: fnb
        $response = $this->get('/opportunities?sector=fnb');
        $response->assertStatus(200);
        $response->assertSee('Tea Station Express');
        $response->assertDontSee('Mega Mart Grocery');

        // Filter by budget: under-5l
        $budgetResponse = $this->get('/opportunities?budget=under-5l');
        $budgetResponse->assertStatus(200);
        $budgetResponse->assertSee('Tea Station Express');
        $budgetResponse->assertDontSee('Mega Mart Grocery');

        // Filter by state: MH
        $stateResponse = $this->get('/opportunities?state=MH');
        $stateResponse->assertStatus(200);
        $stateResponse->assertSee('Mega Mart Grocery');
        $stateResponse->assertDontSee('Tea Station Express');
    }

    /**
     * Test unpublished or draft opportunities are strictly hidden from catalog.
     */
    public function test_draft_opportunities_are_hidden_from_catalog(): void
    {
        $published = Opportunity::factory()->published()->create(['title' => 'Visible Brand']);
        $draft = Opportunity::factory()->draft()->create(['title' => 'Hidden Draft Brand']);

        $response = $this->get('/opportunities');

        $response->assertStatus(200);
        $response->assertSee('Visible Brand');
        $response->assertDontSee('Hidden Draft Brand');
    }

    /**
     * Test opportunity detail page displays full disclosures and provenance.
     */
    public function test_opportunity_detail_page_renders_disclosures(): void
    {
        $company = Company::factory()->create([
            'brand_name' => 'Express Courier',
            'legal_name' => 'Express Logistics India Pvt Ltd',
            'cin' => 'U64120DL2015PTC123456',
        ]);

        $opportunity = Opportunity::factory()->published()->create([
            'company_id' => $company->id,
            'title' => 'Express Courier Hub',
            'slug' => 'express-courier-hub',
        ]);

        OpportunityFinancialBreakdown::factory()->create([
            'opportunity_id' => $opportunity->id,
            'item_name' => 'Initial Licensing Fee',
            'amount' => 150000,
            'source_evidence' => 'Franchise Schedule A 2026',
        ]);

        $response = $this->get('/opportunities/express-courier-hub');

        $response->assertStatus(200);
        $response->assertSee('Express Courier Hub');
        $response->assertSee('Express Logistics India Pvt Ltd');
        $response->assertSee('U64120DL2015PTC123456');
        $response->assertSee('Initial Licensing Fee');
        $response->assertSee('Franchise Schedule A 2026');
        $response->assertSee('Platform Advisory & Regulatory Caveats');
    }

    /**
     * Test accessing an unpublished opportunity detail page returns 404 for guests.
     */
    public function test_unverified_opportunity_detail_returns_404_for_guest(): void
    {
        $draft = Opportunity::factory()->draft()->create(['slug' => 'unverified-kiosk']);

        $response = $this->get('/opportunities/unverified-kiosk');

        $response->assertStatus(404);
    }

    /**
     * Test enquiry submission requires explicit consent.
     */
    public function test_enquiry_requires_mandatory_consent(): void
    {
        $response = $this->post('/enquiry', [
            'full_name' => 'Sunil Verma',
            'phone' => '9876543210',
            'email' => 'sunil@example.com',
            'target_state' => 'UP',
            'target_city' => 'Lucknow',
            'budget_range' => '5l-15l',
            'preferred_timeline' => '1-3-months',
            'commercial_space_status' => 'searching',
            'business_experience' => 'first_time',
            // consent intentionally omitted
        ]);

        $response->assertSessionHasErrors(['consent']);
        $this->assertEquals(0, Enquiry::count());
    }

    /**
     * Test valid consented enquiry submission creates record and redirects to receipt.
     */
    public function test_valid_enquiry_creates_record_and_redirects(): void
    {
        $response = $this->post('/enquiry', [
            'full_name' => 'Sunil Verma',
            'phone' => '9876543210',
            'email' => 'sunil@example.com',
            'target_state' => 'UP',
            'target_city' => 'Lucknow',
            'budget_range' => '5l-15l',
            'preferred_timeline' => '1-3-months',
            'commercial_space_status' => 'searching',
            'business_experience' => 'first_time',
            'consent' => '1',
        ]);

        $response->assertRedirect('/enquiry/confirmed');
        $this->assertEquals(1, Enquiry::count());

        $enquiry = Enquiry::first();
        $this->assertEquals('Sunil Verma', $enquiry->full_name);
        $this->assertTrue($enquiry->consent_given);
        $this->assertNotNull($enquiry->consent_recorded_at);
        $this->assertStringStartsWith('FD-CASE-', $enquiry->reference_code);

        // Test confirmation page
        $confirmResponse = $this->withSession(['enquiry_reference' => $enquiry->reference_code])
            ->get('/enquiry/confirmed');

        $confirmResponse->assertStatus(200);
        $confirmResponse->assertSee($enquiry->reference_code);
    }

    /**
     * Test informational policy pages return 200.
     */
    public function test_policy_pages_load_successfully(): void
    {
        $this->get('/how-it-works')->assertStatus(200)->assertSee('How Managed Coordination Protects Entrepreneurs');
        $this->get('/verification-policy')->assertStatus(200)->assertSee('Opportunity Verification & Publication Policy');
        $this->get('/grievance-and-fraud')->assertStatus(200)->assertSee('Grievance Officer & Fraud Reporting Route');
        $this->get('/privacy-policy')->assertStatus(200)->assertSee('Privacy Notice');
        $this->get('/terms-of-service')->assertStatus(200)->assertSee('Terms of Service & Advisory Disclaimer');
    }
}
