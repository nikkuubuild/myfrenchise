<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\OpportunityFinancialBreakdown;
use App\Models\OpportunityLocation;
use App\Models\OpportunityRequirement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityDataModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Category creation and active scope.
     */
    public function test_category_creation_and_scope(): void
    {
        $active = Category::factory()->create(['is_active' => true]);
        $inactive = Category::factory()->create(['is_active' => false]);

        $activeCategories = Category::active()->get();

        $this->assertTrue($activeCategories->contains($active));
        $this->assertFalse($activeCategories->contains($inactive));
    }

    /**
     * Test Company creation, CIN identification, and verification state.
     */
    public function test_company_creation_and_verified_scope(): void
    {
        $verifiedCompany = Company::factory()->create([
            'status' => 'verified',
            'cin' => 'U55101DL2010PTC200100',
        ]);
        $draftCompany = Company::factory()->create(['status' => 'draft']);

        $this->assertTrue($verifiedCompany->isVerified());
        $this->assertFalse($draftCompany->isVerified());

        $verifiedList = Company::verified()->get();
        $this->assertTrue($verifiedList->contains($verifiedCompany));
        $this->assertFalse($verifiedList->contains($draftCompany));
    }

    /**
     * Test Opportunity creation with full relationships and INR formatting.
     */
    public function test_opportunity_creation_with_financials_locations_and_requirements(): void
    {
        $company = Company::factory()->create(['brand_name' => 'Chai Point']);
        $category = Category::factory()->create(['name' => 'Food & Beverage']);

        $opportunity = Opportunity::factory()->create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'title' => 'Chai Point Express',
            'min_investment' => 850000,
            'max_investment' => 1200000,
            'publication_status' => 'published',
            'last_verified_at' => now()->subDay(),
            'verification_valid_until' => now()->addDays(90),
        ]);

        // Add Financial Breakdown
        $breakdown = OpportunityFinancialBreakdown::factory()->create([
            'opportunity_id' => $opportunity->id,
            'item_name' => 'Franchise Fee',
            'amount' => 250000,
            'source_evidence' => 'Clause 4.1 Agreement',
        ]);

        // Add Territory
        $location = OpportunityLocation::factory()->create([
            'opportunity_id' => $opportunity->id,
            'state_code' => 'UP',
            'district' => 'Lucknow',
            'status' => 'available',
        ]);

        // Add Requirement
        $requirement = OpportunityRequirement::factory()->create([
            'opportunity_id' => $opportunity->id,
            'requirement_type' => 'space',
            'title' => 'Minimum 150 Sq. Ft.',
            'is_mandatory' => true,
        ]);

        // Assert relationships
        $this->assertEquals('Chai Point', $opportunity->company->brand_name);
        $this->assertEquals('Food & Beverage', $opportunity->category->name);
        $this->assertCount(1, $opportunity->financialBreakdowns);
        $this->assertEquals(250000, $opportunity->financialBreakdowns->first()->amount);
        $this->assertCount(1, $opportunity->locations);
        $this->assertEquals('Lucknow', $opportunity->locations->first()->district);
        $this->assertCount(1, $opportunity->requirements);
        $this->assertTrue($opportunity->requirements->first()->is_mandatory);

        // Assert formatting and verification status
        $this->assertTrue($opportunity->isVerified());
        $this->assertEquals('₹8.5 Lakh - ₹12 Lakh', $opportunity->formattedInvestmentRange());
    }

    /**
     * Test published scope only returns opportunities that are published with non-expired verification.
     */
    public function test_published_scope_requires_verification_and_published_status(): void
    {
        $validOpportunity = Opportunity::factory()->published()->create();

        $draftOpportunity = Opportunity::factory()->draft()->create();

        $expiredVerification = Opportunity::factory()->create([
            'publication_status' => 'published',
            'last_verified_at' => now()->subDays(200),
            'verification_valid_until' => now()->subDays(10), // Expired verification
        ]);

        $published = Opportunity::published()->get();

        $this->assertTrue($published->contains($validOpportunity));
        $this->assertFalse($published->contains($draftOpportunity));
        $this->assertFalse($published->contains($expiredVerification));
    }

    /**
     * Test budget query filter.
     */
    public function test_in_budget_scope_filters_correctly(): void
    {
        $lowBudget = Opportunity::factory()->create([
            'min_investment' => 200000,
            'max_investment' => 500000,
        ]);

        $highBudget = Opportunity::factory()->create([
            'min_investment' => 1500000,
            'max_investment' => 3000000,
        ]);

        // Filter for budget range ₹4L - ₹8L (should match $lowBudget since its max is 5L, not highBudget)
        $results = Opportunity::inBudget(400000, 800000)->get();

        $this->assertTrue($results->contains($lowBudget));
        $this->assertFalse($results->contains($highBudget));
    }
}
