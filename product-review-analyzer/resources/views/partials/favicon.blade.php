{{-- RenalSight — Favicon & Meta Head --}}
<link rel="icon" type="image/x-icon"  href="{{ asset('renalsight-favicons/favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('renalsight-favicons/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('renalsight-favicons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('renalsight-favicons/favicon-96x96.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('renalsight-favicons/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('renalsight-favicons/site.webmanifest') }}">
<meta name="msapplication-config"   content="{{ asset('renalsight-favicons/browserconfig.xml') }}">
<meta name="msapplication-TileColor" content="#6A0DAD">
<meta name="msapplication-TileImage" content="{{ asset('renalsight-favicons/mstile-150x150.png') }}">
<meta name="theme-color" content="#6A0DAD">
<meta property="og:title"       content="{{ $title ?? 'RenalSight' }}">
<meta property="og:description" content="{{ $description ?? 'Review Analytics Insight' }}">
<meta property="og:image"       content="{{ asset('renalsight-favicons/og-image.png') }}">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:type"        content="website">
<meta property="og:url"         content="{{ url()->current() }}">
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="{{ $title ?? 'RenalSight' }}">
<meta name="twitter:description" content="{{ $description ?? 'Review Analytics Insight' }}">
<meta name="twitter:image"       content="{{ asset('renalsight-favicons/og-image.png') }}">
