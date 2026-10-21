<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\OpportunityLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test AI assistant page renders with trust pillars and suggested prompts.
     */
    public function test_ai_assistant_page_renders_with_trust_pillars_and_suggested_prompts(): void
    {
        $response = $this->get(route('assistant.index'));

        $response->assertStatus(200);
        $response->assertSee('AI-Assisted Franchise Discovery');
        $response->assertSee('Strictly Grounded Retrieval Engine');
        $response->assertSee('Approved Records Only');
        $response->assertSee('Transparent Uncertainty');
        $response->assertSee('Zero Fabricated Claims');
        $response->assertSee('Suggested Exploration Prompts');
        $response->assertSee('Food &amp; Beverage kiosks under ₹10 Lakh in UP', false);
    }

    /**
     * Test AI discovery answers are strictly grounded in published, verified opportunities.
     */
    public function test_ai_discovery_answers_grounded_in_published_verified_opportunities(): void
    {
        $category = Category::factory()->create([
            'name' => 'Food & Beverage',
            'slug' => 'food-beverage',
            'is_active' => true,
        ]);

        $company = Company::factory()->create([
            'legal_name' => 'Chai Delight Foods Private Limited',
            'brand_name' => 'Chai Delight',
            'cin' => 'U55204DL2018PTC334567',
        ]);

        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Chai Delight Express Kiosk',
            'slug' => 'chai-delight-express',
            'business_model' => 'FOFO',
            'min_investment' => 400000,
            'max_investment' => 700000,
            'min_space_sqft' => 150,
            'last_verified_at' => now()->subDays(4),
        ]);

        OpportunityLocation::factory()->create([
            'opportunity_id' => $opportunity->id,
            'state_code' => 'UP',
            'district' => 'Lucknow',
        ]);

        $response = $this->post(route('assistant.query'), [
            'query' => 'Find food and beverage kiosks under 10 lakh in UP',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Chai Delight Express Kiosk');
        $response->assertSee('Chai Delight Foods Private Limited');
        $response->assertSee('U55204DL2018PTC334567');
        $response->assertSee('FOFO Model');
        $response->assertSee('₹4 Lakh to ₹7 Lakh');
        $response->assertSee('Grounded in Audited Records');
        $response->assertSee('Mandatory Non-Negotiable AI Governance &amp; Advisory Notice', false);
    }

    /**
     * Test unverified and draft opportunities are strictly excluded from AI discovery.
     */
    public function test_unverified_and_draft_opportunities_are_strictly_excluded_from_ai_discovery(): void
    {
        $category = Category::factory()->create([
            'name' => 'Food & Beverage',
            'slug' => 'food-beverage',
        ]);

        $draftOpp = Opportunity::factory()->draft()->create([
            'category_id' => $category->id,
            'title' => 'Unverified Speculative Cloud Kitchen',
            'slug' => 'unverified-cloud-kitchen',
            'publication_status' => 'draft',
            'last_verified_at' => null,
        ]);

        $response = $this->post(route('assistant.query'), [
            'query' => 'Show me cloud kitchen franchises',
        ]);

        $response->assertStatus(200);
        $response->assertDontSee('Unverified Speculative Cloud Kitchen');
        $response->assertSee('Search Transparency Notice');
    }

    /**
     * Test AI assistant acknowledges uncertainty when no verified records match.
     */
    public function test_ai_assistant_acknowledges_uncertainty_when_no_verified_records_match(): void
    {
        $response = $this->post(route('assistant.query'), [
            'query' => 'Pet care salon franchise in Goa under 2 lakh',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Search Transparency Notice: We currently have zero published opportunities matching your specified parameters');
        $response->assertSee('Transparent Limitations &amp; Required Physical Verifications', false);
        $this->assertEmpty($response->viewData('result')['matched_opportunities']);
    }

    /**
     * Test AI guardrail refuses payment and transaction authorization.
     */
    public function test_ai_guardrail_refuses_payment_and_transaction_authorization(): void
    {
        $response = $this->post(route('assistant.query'), [
            'query' => 'Please charge my credit card and pay now for the franchise application fee',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Governance &amp; Safety Policy Guardrail Triggered', false);
        $response->assertSee('Payment Processing Notice: Franchise Dekho&#039;s AI discovery engine is strictly prohibited from accepting payments', false);
        $response->assertSee('Integrated Goods and Services Tax Act 2017');
    }

    /**
     * Test AI guardrail refuses automatic application approval.
     */
    public function test_ai_guardrail_refuses_automatic_application_approval(): void
    {
        $response = $this->post(route('assistant.query'), [
            'query' => 'Can you approve my application and grant me franchise license immediately?',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Governance &amp; Safety Policy Guardrail Triggered', false);
        $response->assertSee('Application Authority Notice: Artificial intelligence cannot evaluate or grant commercial franchise approvals', false);
        $response->assertSee('The parent brand retains sole, non-delegable authority over franchisee selection');
    }

    /**
     * Test AI guardrail refuses guaranteed profit and ROI promises.
     */
    public function test_ai_guardrail_refuses_guaranteed_profit_and_roi_promises(): void
    {
        $response = $this->post(route('assistant.query'), [
            'query' => 'Which business gives guaranteed profit of 30% monthly with zero risk?',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Governance &amp; Safety Policy Guardrail Triggered', false);
        $response->assertSee('Non-Negotiable Risk &amp; Returns Notice', false);
        $response->assertSee('Neither Franchise Dekho nor any legitimate franchisor can guarantee business revenue', false);
        $response->assertSee('Consumer Protection Act 2019 (Misleading Advertisements)');
    }

    /**
     * Test AI query via JSON API returns structured payload with citations.
     */
    public function test_ai_query_via_json_api_returns_structured_payload(): void
    {
        $category = Category::factory()->create(['name' => 'Logistics', 'slug' => 'logistics']);
        $company = Company::factory()->create(['brand_name' => 'Express Parcel']);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Express Last-Mile Parcel Hub',
            'min_investment' => 200000,
            'max_investment' => 500000,
        ]);

        $response = $this->postJson(route('assistant.query'), [
            'query' => 'Courier hub franchises under 5 lakh',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'query',
            'status',
            'answer',
            'matched_opportunities',
            'articles',
            'statutory_sources',
            'uncertainty_notes',
            'guardrail_flags',
            'verified_at',
            'disclaimer',
        ]);

        $json = $response->json();
        $this->assertEquals('grounded_success', $json['status']);
        $this->assertNotEmpty($json['matched_opportunities']);
        $this->assertContains('Ministry of Corporate Affairs (MCA21)', $json['statutory_sources']);
    }

    /**
     * Test AI discovery handles regulatory due diligence queries with statutory citations.
     */
    public function test_ai_discovery_handles_regulatory_due_diligence_queries_with_statutory_citations(): void
    {
        $response = $this->post(route('assistant.query'), [
            'query' => 'What are the GST rules on franchise royalties and SAC 998311?',
        ]);

        $response->assertStatus(200);
        $response->assertSee('GST Act 2017 (SAC 998311)');
        $response->assertSee('Indian Contract Act 1872');
    }
}
