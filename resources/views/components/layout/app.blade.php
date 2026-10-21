@props([
    'title' => 'Franchise Dekho — India-Wide Managed Business Opportunity Discovery',
    'metaDescription' => 'Discover verified Indian franchise, dealership, and business opportunities. Independent evidence verification and human-coordinated application management.',
    'canonical' => null,
    'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
    'ogType' => 'website',
    'ogImage' => null,
    'schema' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <!-- Open Graph Metadata -->
    <meta property="og:site_name" content="Franchise Dekho">
    <meta property="og:locale" content="en_IN">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Twitter Card Metadata -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <!-- Typography: Editorial Serif & Precision Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,600;0,8..60,700;1,8..60,400&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Schema.org JSON-LD Structured Data -->
    @if(!empty($schema))
        <script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
    @endif
    @stack('schema')
</head>
<body class="min-h-full flex flex-col bg-surface-canvas text-ink-primary antialiased selection:bg-accent-green-bg selection:text-status-verified-text">
    <!-- Header -->
    <x-layout.header />

    <!-- Main Content -->
    <main id="main-content" class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-layout.footer />

    <!-- Cookie & Privacy Consent Banner -->
    <x-layout.cookie-banner />
</body>
</html>
