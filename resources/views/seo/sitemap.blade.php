{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Public Core Pages -->
    @foreach($staticRoutes as $route)
    <url>
        <loc>{{ $route['url'] }}</loc>
        <lastmod>{{ $route['lastmod'] }}</lastmod>
        <changefreq>{{ $route['changefreq'] }}</changefreq>
        <priority>{{ $route['priority'] }}</priority>
    </url>
    @endforeach

    <!-- Sector / Category Landing Pages -->
    @foreach($categories as $category)
    <url>
        <loc>{{ route('sectors.show', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    <!-- Verified Business Opportunities -->
    @foreach($opportunities as $opp)
    <url>
        <loc>{{ route('opportunities.show', $opp->slug) }}</loc>
        <lastmod>{{ ($opp->last_verified_at ?? $opp->updated_at)->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    <!-- Editorial Due Diligence & Knowledge Guides -->
    @foreach($articles as $article)
    <url>
        <loc>{{ route('guides.show', $article->slug) }}</loc>
        <lastmod>{{ ($article->last_verified_at ?? $article->updated_at)->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
