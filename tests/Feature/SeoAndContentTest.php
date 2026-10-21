<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndContentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test XML sitemap contains indexable canonical URLs and returns valid XML.
     */
    public function test_xml_sitemap_returns_valid_xml_with_canonical_urls(): void
    {
        $category = Category::factory()->create(['name' => 'Food & Beverage', 'slug' => 'food-beverage', 'is_active' => true]);
        $company = Company::factory()->create(['brand_name' => 'Chai Station']);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Chai Station Express Kiosk',
            'slug' => 'chai-station-express',
            'last_verified_at' => now()->subDays(5),
        ]);

        $article = Article::create([
            'title' => 'Understanding Franchise Agreements in India',
            'slug' => 'understanding-franchise-agreements-india',
            'category_id' => $category->id,
            'author_name' => 'Franchise Dekho Research Desk',
            'author_title' => 'Legal Analyst',
            'reading_time_minutes' => 6,
            'excerpt' => 'A guide to understanding binding franchise contracts under Indian law.',
            'content' => 'Full legal analysis of contract terms...',
            'is_published' => true,
            'published_at' => now()->subDays(10),
            'last_verified_at' => now()->subDays(2),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        // Verify valid XML root
        $content = $response->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);

        // Core static routes
        $this->assertStringContainsString(url('/'), $content);
        $this->assertStringContainsString(route('opportunities.index'), $content);
        $this->assertStringContainsString(route('guides.index'), $content);
        $this->assertStringContainsString(route('pages.how-it-works'), $content);
        $this->assertStringContainsString(route('pages.verification-policy'), $content);

        // Dynamic sector, opportunity, and guide URLs
        $this->assertStringContainsString(route('sectors.show', 'food-beverage'), $content);
        $this->assertStringContainsString(route('opportunities.show', 'chai-station-express'), $content);
        $this->assertStringContainsString(route('guides.show', 'understanding-franchise-agreements-india'), $content);
    }

    /**
     * Test XML sitemap strictly excludes unverified opportunities and private routes.
     */
    public function test_xml_sitemap_excludes_unverified_opportunities_and_private_routes(): void
    {
        $unverifiedOpp = Opportunity::factory()->create([
            'title' => 'Unverified Draft Franchise',
            'slug' => 'unverified-draft-franchise',
            'publication_status' => 'draft',
            'last_verified_at' => null,
        ]);

        $draftArticle = Article::create([
            'title' => 'Draft Internal Guide',
            'slug' => 'draft-internal-guide',
            'author_name' => 'Internal Staff',
            'author_title' => 'Researcher',
            'reading_time_minutes' => 3,
            'excerpt' => 'Internal drafting note.',
            'content' => 'Draft body...',
            'is_published' => false,
            'last_verified_at' => null,
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();

        // Must not contain draft or unverified entities
        $this->assertStringNotContainsString('unverified-draft-franchise', $content);
        $this->assertStringNotContainsString('draft-internal-guide', $content);

        // Must not contain private operational routes
        $this->assertStringNotContainsString('/admin/', $content);
        $this->assertStringNotContainsString('/customer/', $content);
        $this->assertStringNotContainsString('/checkout/', $content);
        $this->assertStringNotContainsString('/health', $content);
        $this->assertStringNotContainsString('/webhooks/', $content);
    }

    /**
     * Test robots.txt returns 200 with proper crawl directives and sitemap link.
     */
    public function test_robots_txt_returns_proper_directives_and_sitemap_url(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $content = $response->getContent();

        // Public allowances
        $this->assertStringContainsString('Allow: /opportunities', $content);
        $this->assertStringContainsString('Allow: /sectors/', $content);
        $this->assertStringContainsString('Allow: /guides/', $content);

        // Private disallows
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /customer/', $content);
        $this->assertStringContainsString('Disallow: /checkout/', $content);
        $this->assertStringContainsString('Disallow: /webhooks/', $content);
        $this->assertStringContainsString('Disallow: /health', $content);

        // AI crawlers
        $this->assertStringContainsString('User-agent: GPTBot', $content);
        $this->assertStringContainsString('User-agent: ClaudeBot', $content);
        $this->assertStringContainsString('User-agent: PerplexityBot', $content);
        $this->assertStringContainsString('User-agent: CCBot', $content);

        // Sitemap pointer
        $this->assertStringContainsString('Sitemap:', $content);
        $this->assertStringContainsString('/sitemap.xml', $content);
    }

    /**
     * Test canonical URL, OpenGraph, and Twitter tags render accurately on public pages.
     */
    public function test_meta_tags_canonical_and_opengraph_render_accurately(): void
    {
        $category = Category::factory()->create(['name' => 'Logistics', 'slug' => 'logistics', 'is_active' => true]);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Speedy Express Courier Hub',
            'slug' => 'speedy-express-hub',
            'summary' => 'Last-mile logistics hub franchise with institutional support.',
        ]);

        $response = $this->get(route('opportunities.show', $opportunity->slug));

        $response->assertStatus(200);

        // Canonical tag
        $response->assertSee('<link rel="canonical" href="' . route('opportunities.show', $opportunity->slug) . '">', false);

        // Robots default on public page
        $response->assertSee('<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">', false);

        // OpenGraph tags
        $response->assertSee('<meta property="og:site_name" content="Franchise Dekho">', false);
        $response->assertSee('<meta property="og:locale" content="en_IN">', false);
        $response->assertSee('<meta property="og:url" content="' . route('opportunities.show', $opportunity->slug) . '">', false);

        // Twitter card
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    /**
     * Test private/protected pages render noindex, nofollow robots directive.
     */
    public function test_private_views_render_noindex_nofollow_directive(): void
    {
        // 1. Guest Auth Page
        $authResponse = $this->get('/login');
        $authResponse->assertStatus(200);
        $authResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        // 2. Customer Dashboard
        $customer = User::factory()->customer()->create();
        $dashboardResponse = $this->actingAs($customer)->get('/customer/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        // 3. Admin Cases Pipeline
        $staff = User::factory()->caseManager()->create();
        $adminResponse = $this->actingAs($staff)->get('/admin/cases');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        // 4. Checkout Page
        $this->seed(\Database\Seeders\ServicePackageSeeder::class);
        $package = ServicePackage::firstOrFail();
        $checkoutResponse = $this->get(route('checkout.show', $package->slug));
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /**
     * Test structured data Organization and WebSite schemas render on homepage.
     */
    public function test_structured_data_organization_and_website_rendered_on_homepage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Check JSON-LD script is present
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type": "Organization"', false);
        $response->assertSee('"name": "Franchise Dekho"', false);
        $response->assertSee('"addressRegion": "Haryana"', false);
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "SearchAction"', false);
    }

    /**
     * Test opportunity detail renders valid Service schema and BreadcrumbList without fake ratings.
     */
    public function test_opportunity_detail_renders_valid_service_schema_and_breadcrumbs(): void
    {
        $category = Category::factory()->create(['name' => 'Healthcare', 'slug' => 'healthcare', 'is_active' => true]);
        $company = Company::factory()->create([
            'brand_name' => 'Apollo Diagnostics Clinic',
            'legal_name' => 'Apollo Health Services Ltd',
        ]);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Apollo Diagnostics Collection Center',
            'slug' => 'apollo-diagnostics-center',
            'min_investment' => 800000,
            'max_investment' => 1500000,
            'last_verified_at' => now()->subDays(3),
        ]);

        $response = $this->get(route('opportunities.show', $opportunity->slug));

        $response->assertStatus(200);

        // Service Schema
        $response->assertSee('"@type": "Service"', false);
        $response->assertSee('"serviceType": "Franchise Opportunity"', false);
        $response->assertSee('"@type": "AggregateOffer"', false);
        $response->assertSee('"priceCurrency": "INR"', false);
        $response->assertSee('800000', false);
        $response->assertSee('1500000', false);

        // BreadcrumbList Schema
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee('"name": "Healthcare"', false);

        // Strictly NO fabricated ratings or reviews
        $response->assertDontSee('"aggregateRating"', false);
        $response->assertDontSee('"ratingValue"', false);
        $response->assertDontSee('"reviewCount"', false);
    }

    /**
     * Test sector landing page renders regulatory guidance and opportunities.
     */
    public function test_sector_landing_page_renders_regulatory_guidance_and_opportunities(): void
    {
        $category = Category::factory()->create([
            'name' => 'Food & Beverage (QSR)',
            'slug' => 'food-beverage',
            'description' => 'Quick-service restaurant formats and beverage kiosks.',
            'is_active' => true,
        ]);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Chai Tapri Express',
        ]);

        $response = $this->get(route('sectors.show', 'food-beverage'));

        $response->assertStatus(200);
        $response->assertSee('Food Safety and Standards Act (FSSAI) 2006');
        $response->assertSee('Chai Tapri Express');
        $response->assertSee('"@type": "CollectionPage"', false);
        $response->assertSee('<link rel="canonical" href="' . route('sectors.show', 'food-beverage') . '">', false);
    }

    /**
     * Test article knowledge hub and detail page render statutory citations.
     */
    public function test_article_knowledge_hub_and_detail_render_statutory_sources(): void
    {
        $article = Article::create([
            'title' => 'Commercial Lease Due Diligence for Franchisees',
            'slug' => 'commercial-lease-due-diligence-franchisees',
            'author_name' => 'Franchise Dekho Research Desk',
            'author_title' => 'Commercial Real Estate Analyst',
            'reading_time_minutes' => 7,
            'excerpt' => 'Critical lease covenants, CAM charges, and municipal zoning compliance in India.',
            'content' => 'Full analysis of commercial lease covenants under Transfer of Property Act 1882...',
            'sources_and_evidence' => 'Transfer of Property Act 1882; Municipal Corporation Building Bye-Laws.',
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'last_verified_at' => now()->subDays(1),
        ]);

        // 1. Knowledge Hub Index
        $indexResponse = $this->get(route('guides.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Commercial Lease Due Diligence for Franchisees');
        $indexResponse->assertSee('7 min read');

        // 2. Article Detail Page
        $detailResponse = $this->get(route('guides.show', $article->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Transfer of Property Act 1882');
        $detailResponse->assertSee('"@type": "Article"', false);
        $detailResponse->assertSee('Non-Negotiable Regulatory & Advisory Notice');
        $detailResponse->assertSee('Audit Verified');
    }

    /**
     * Test admin can view editorial queue and re-verify content freshness.
     */
    public function test_admin_can_manage_articles_and_update_content_freshness(): void
    {
        $staff = User::factory()->caseManager()->create();
        $oldDate = now()->subDays(200);

        $article = Article::create([
            'title' => 'Master Franchise Rights and FMCG Regulations',
            'slug' => 'master-franchise-rights-fmcg',
            'author_name' => 'Franchise Dekho Research Desk',
            'author_title' => 'Regulatory Specialist',
            'reading_time_minutes' => 5,
            'excerpt' => 'Master franchise guidelines under Indian regulatory statutes.',
            'content' => 'Detailed legal content...',
            'is_published' => true,
            'published_at' => now()->subDays(210),
            'last_verified_at' => $oldDate,
        ]);

        // 1. Staff views queue with freshness alert
        $indexResponse = $this->actingAs($staff)->get(route('admin.articles.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Master Franchise Rights and FMCG Regulations');
        $indexResponse->assertSee('Audit Overdue');

        // 2. Staff re-verifies content freshness today
        $verifyResponse = $this->actingAs($staff)->post(route('admin.articles.verify', $article));
        $verifyResponse->assertRedirect();

        $article->refresh();
        $this->assertTrue($article->last_verified_at->isToday());
        $this->assertTrue($article->isFresh());

        // 3. Staff authors a new editorial guide
        $createResponse = $this->actingAs($staff)->post(route('admin.articles.store'), [
            'title' => 'Zoning Laws for Cloud Kitchens in Delhi NCR',
            'author_name' => 'Urban Compliance Desk',
            'author_title' => 'Municipal Law Specialist',
            'reading_time_minutes' => 6,
            'excerpt' => 'Municipal licensing and Master Plan 2041 zoning rules.',
            'content' => 'Detailed cloud kitchen compliance...',
            'is_published' => 1,
        ]);

        $createResponse->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'title' => 'Zoning Laws for Cloud Kitchens in Delhi NCR',
            'slug' => 'zoning-laws-for-cloud-kitchens-in-delhi-ncr',
            'is_published' => true,
        ]);
    }
}
