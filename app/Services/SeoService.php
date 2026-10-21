<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Opportunity;

class SeoService
{
    /**
     * Generate Schema.org Organization structured data for Franchise Dekho.
     */
    public function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Franchise Dekho',
            'legalName' => 'Franchise Dekho Technologies LLP',
            'url' => config('app.url'),
            'logo' => url('/images/logo.png'),
            'description' => 'India-wide managed business-opportunity discovery and application-coordination platform with independent verification and human case management.',
            'foundingLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Gurugram',
                    'addressRegion' => 'Haryana',
                    'addressCountry' => 'IN',
                ],
            ],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => '+91-124-4001234',
                    'contactType' => 'customer service',
                    'areaServed' => 'IN',
                    'availableLanguage' => ['en', 'hi'],
                ],
                [
                    '@type' => 'ContactPoint',
                    'email' => 'grievance@franchisedekho.com',
                    'contactType' => 'Grievance Officer',
                    'areaServed' => 'IN',
                ],
            ],
            'sameAs' => [
                'https://twitter.com/FranchiseDekho',
                'https://linkedin.com/company/franchisedekho',
            ],
        ];
    }

    /**
     * Generate Schema.org WebSite structured data with SearchAction.
     */
    public function webSiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Franchise Dekho',
            'url' => config('app.url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/opportunities') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate Schema.org BreadcrumbList structured data.
     * $crumbs is an array of ['name' => '...', 'url' => '...']
     */
    public function breadcrumbsSchema(array $crumbs): array
    {
        $itemList = [];
        $position = 1;

        foreach ($crumbs as $crumb) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $crumb['name'],
                'item' => $crumb['url'] ?? null,
            ];
            $position++;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    /**
     * Generate Schema.org Service / Business Opportunity structured data for a verified Opportunity.
     * Note: Per SEO_CONTENT.md guidelines, no fabricated reviews, stars, or ratings are included.
     */
    public function opportunitySchema(Opportunity $opportunity): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $opportunity->title,
            'serviceType' => 'Franchise Opportunity',
            'description' => $opportunity->summary,
            'provider' => [
                '@type' => 'Organization',
                'name' => $opportunity->company->brand_name,
                'legalName' => $opportunity->company->legal_name,
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'India',
            ],
            'offers' => [
                '@type' => 'AggregateOffer',
                'priceCurrency' => 'INR',
                'lowPrice' => $opportunity->min_investment,
                'highPrice' => $opportunity->max_investment,
                'priceValidUntil' => $opportunity->verification_valid_until?->toDateString(),
            ],
            'category' => $opportunity->category?->name,
            'url' => route('opportunities.show', $opportunity->slug),
        ];

        if ($opportunity->last_verified_at) {
            $schema['dateModified'] = $opportunity->last_verified_at->toIso8601String();
        }

        return $schema;
    }

    /**
     * Generate Schema.org Article structured data for an editorial guide.
     */
    public function articleSchema(Article $article): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'url' => route('guides.show', $article->slug),
            'author' => [
                '@type' => 'Organization',
                'name' => $article->author_name ?? 'Franchise Dekho Research Desk',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Franchise Dekho',
                'url' => config('app.url'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('/images/logo.png'),
                ],
            ],
            'datePublished' => $article->published_at?->toIso8601String() ?? $article->created_at->toIso8601String(),
            'dateModified' => $article->last_verified_at?->toIso8601String() ?? $article->updated_at->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('guides.show', $article->slug),
            ],
        ];
    }

    /**
     * Generate Schema.org ItemList / CollectionPage structured data for a Sector.
     */
    public function sectorSchema(Category $category, $opportunities): array
    {
        $items = [];
        $pos = 1;

        foreach ($opportunities as $opp) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'url' => route('opportunities.show', $opp->slug),
                'name' => $opp->title,
            ];
            $pos++;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name . ' Franchise & Dealership Opportunities in India',
            'description' => $category->description,
            'url' => route('sectors.show', $category->slug),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($items),
                'itemListElement' => $items,
            ],
        ];
    }
}
