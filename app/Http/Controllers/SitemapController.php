<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Opportunity;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap containing canonical, indexable public URLs.
     */
    public function index(): Response
    {
        $staticRoutes = [
            ['url' => url('/'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => route('opportunities.index'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['url' => route('assessment.index'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['url' => route('enquiry.create'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('guides.index'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['url' => route('pages.how-it-works'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('pages.verification-policy'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('pages.grievance'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['url' => route('pages.privacy'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['url' => route('pages.terms'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['url' => route('pages.cookie-policy'), 'lastmod' => now()->startOfMonth()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        // 1. Sector landing pages
        $categories = Category::active()->get();

        // 2. Only published and verified opportunities
        $opportunities = Opportunity::published()
            ->select(['id', 'slug', 'updated_at', 'last_verified_at'])
            ->get();

        // 3. Only published editorial articles
        $articles = Article::published()
            ->select(['id', 'slug', 'updated_at', 'last_verified_at', 'published_at'])
            ->get();

        $content = view('seo.sitemap', compact('staticRoutes', 'categories', 'opportunities', 'articles'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('X-Robots-Tag', 'noindex');
    }
}
