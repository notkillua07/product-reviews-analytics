{{-- ============================================================
     Renalsight — Favicon & Meta Head Snippet
     Place inside <head> of your layouts/app.blade.php
     ============================================================ --}}

{{-- Primary Favicon --}}
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">

{{-- Apple Touch Icon --}}
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

{{-- Android / PWA --}}
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

{{-- Windows Tiles --}}
<meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
<meta name="msapplication-TileColor" content="#6A0DAD">
<meta name="msapplication-TileImage" content="{{ asset('mstile-150x150.png') }}">

{{-- Theme Color (mobile browser chrome) --}}
<meta name="theme-color" content="#6A0DAD">

{{-- Open Graph / Social Sharing --}}
<meta property="og:title" content="{{ $title ?? 'Renalsight' }}">
<meta property="og:description" content="{{ $description ?? 'Review Analytics Insight' }}">
<meta property="og:image" content="{{ asset('og-image.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title ?? 'Renalsight' }}">
<meta name="twitter:description" content="{{ $description ?? 'Review Analytics Insight' }}">
<meta name="twitter:image" content="{{ asset('og-image.png') }}">
