@php
    // Determine title
    $title = $seo_title ?? null;
    if (!$title && isset($page)) {
        $title = $page->og_title ?: $page->title;
    }
    $title = $title ? $title . ' | ' . config('app.name', 'CubicZahi') : config('app.name', 'CubicZahi');

    // Determine description
    $description = $seo_description ?? null;
    if (!$description && isset($page)) {
        $description = $page->meta_description;
    }

    // Determine keywords
    $keywords = $seo_keywords ?? null;
    if (!$keywords && isset($page)) {
        $keywords = $page->meta_keywords;
    }

    // Determine canonical
    $canonical = $seo_canonical ?? null;
    if (!$canonical && isset($page)) {
        $canonical = $page->canonical_url;
    }

    // Open Graph
    $ogTitle = $seo_og_title ?? (isset($page) ? ($page->og_title ?: $page->title) : config('app.name', 'CubicZahi'));
    $ogDescription = $seo_og_description ?? (isset($page) ? ($page->og_description ?: $page->meta_description) : '');
    $ogUrl = $seo_og_url ?? (isset($page) ? ($page->og_url ?: request()->url()) : request()->url());
    $ogType = $seo_og_type ?? (isset($page) ? ($page->og_type ?: 'website') : 'website');
    $ogSiteName = $seo_og_site_name ?? (isset($page) ? ($page->og_site_name ?: config('app.name')) : config('app.name'));
    $ogLocale = $seo_og_locale ?? (isset($page) ? ($page->og_locale ?: app()->getLocale()) : app()->getLocale());

    // Twitter Cards
    $twitterCard = $seo_twitter_card ?? (isset($page) ? ($page->twitter_card ?: 'summary_large_image') : 'summary_large_image');
    $twitterTitle = $seo_twitter_title ?? (isset($page) ? ($page->twitter_title ?: $ogTitle) : $ogTitle);
    $twitterDescription = $seo_twitter_description ?? (isset($page) ? ($page->twitter_description ?: $ogDescription) : $ogDescription);
@endphp

<title>{{ $title }}</title>

@if(!empty($description))
    <meta name="description" content="{{ $description }}">
@endif

@if(!empty($keywords))
    <meta name="keywords" content="{{ $keywords }}">
@endif

@if(!empty($canonical))
    <link rel="canonical" href="{{ $canonical }}">
@endif

{{-- Open Graph / Facebook --}}
<meta property="og:title" content="{{ $ogTitle }}">
@if(!empty($ogDescription))
    <meta property="og:description" content="{{ $ogDescription }}">
@endif
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $ogSiteName }}">
<meta property="og:locale" content="{{ $ogLocale }}">

{{-- Twitter Cards --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
@if(!empty($twitterDescription))
    <meta name="twitter:description" content="{{ $twitterDescription }}">
@endif

@stack('seo')
