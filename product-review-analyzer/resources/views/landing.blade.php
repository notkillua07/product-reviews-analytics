<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RenalSight — Turn Reviews into Insights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('partials.favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --indigo: #4f46e5;
            --indigo-dark: #4338ca;
            --indigo-light: #eef2ff;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1e293b;
        }

        /* ── Navbar ── */
        .navbar {
            padding: 1rem 0;
            transition: box-shadow 0.2s;
        }
        .navbar.scrolled {
            box-shadow: 0 1px 12px rgba(0,0,0,.08);
        }
        .navbar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--indigo);
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-indigo {
            background: var(--indigo);
            border-color: var(--indigo);
            color: #fff;
        }
        .btn-indigo:hover {
            background: var(--indigo-dark);
            border-color: var(--indigo-dark);
            color: #fff;
        }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
            padding: 6rem 0 5rem;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 70% 50%, rgba(99,102,241,.18) 0%, transparent 60%),
                              radial-gradient(circle at 20% 80%, rgba(139,92,246,.12) 0%, transparent 50%);
        }

        /* Floating decorative orbs */
        .hero-orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(90px);
            pointer-events: none;
            opacity: 0;
            animation: orbFloat var(--orb-dur, 10s) var(--orb-delay, 0s) ease-in-out infinite,
                       orbFadeIn 1.2s var(--orb-fi-delay, 0.3s) ease forwards;
        }
        .hero-orb-1 {
            --orb-dur: 9s; --orb-delay: 0s; --orb-fi-delay: 0.3s;
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(99,102,241,.6), transparent 70%);
            top: -120px; right: -60px;
        }
        .hero-orb-2 {
            --orb-dur: 12s; --orb-delay: 2s; --orb-fi-delay: 0.6s;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(139,92,246,.5), transparent 70%);
            bottom: -80px; left: 8%;
        }
        .hero-orb-3 {
            --orb-dur: 15s; --orb-delay: 1s; --orb-fi-delay: 0.9s;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(167,139,250,.45), transparent 70%);
            top: 40%; left: 42%;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 999px;
            padding: 0.3rem 0.9rem;
            font-size: 0.8rem;
            color: #c7d2fe;
            margin-bottom: 1.5rem;
            animation: fadeSlideUp 0.55s ease both,
                       badgePulse 3.5s 1.2s ease-in-out infinite;
        }
        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.15;
            color: #fff;
            animation: fadeSlideUp 0.6s 0.12s ease both;
        }
        .hero h1 span {
            background: linear-gradient(90deg, #a5b4fc, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-sub {
            color: #94a3b8;
            font-size: 1.1rem;
            max-width: 520px;
            animation: fadeSlideUp 0.6s 0.24s ease both;
        }
        .hero-btns {
            animation: fadeSlideUp 0.6s 0.38s ease both;
        }
        .hero-note {
            animation: fadeSlideUp 0.55s 0.5s ease both;
        }
        .hero-mockup {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 1rem;
            padding: 1.5rem;
            backdrop-filter: blur(4px);
            animation: slideInRight 0.75s 0.22s ease both;
        }
        .sentiment-bar-wrap { margin-bottom: 0.6rem; }
        .sentiment-label { font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem; }
        .sentiment-bar {
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            overflow: hidden;
        }
        .sentiment-bar-fill {
            height: 100%;
            border-radius: 999px;
            width: 0;
            transition: width 1.3s cubic-bezier(.25, 1, .5, 1);
        }
        .review-chip {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
            font-size: 0.75rem;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .review-chip.chip-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .star { color: #fbbf24; font-size: 0.75rem; }

        /* ── Stats bar ── */
        .stats-bar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem 0;
        }
        .stat-item {
            text-align: center;
            padding: 0 1.5rem;
            border-right: 1px solid #e2e8f0;
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--indigo);
        }
        .stat-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.1rem;
        }

        /* ── Features ── */
        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--indigo);
        }
        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 0.75rem;
            background: var(--indigo-light);
            color: var(--indigo);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.75rem;
            height: 100%;
            transition: box-shadow .25s, transform .25s, border-color .25s;
            background: #fff;
        }
        .feature-card:hover {
            box-shadow: 0 12px 36px rgba(79,70,229,.12);
            transform: translateY(-5px);
            border-color: #c7d2fe;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-4deg);
            background: #e0e7ff;
        }

        /* ── How it works ── */
        .how-section { background: #f8fafc; }
        .step-number {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: var(--indigo);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .step-wrap:hover .step-number {
            transform: scale(1.12);
            box-shadow: 0 4px 16px rgba(79,70,229,.35);
        }
        .step-connector {
            width: 2px;
            flex: 1;
            background: #e2e8f0;
            margin: 0.3rem auto;
        }

        /* ── Testimonials ── */
        .testimonial-card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.75rem;
            background: #fff;
            height: 100%;
            transition: box-shadow .25s, transform .25s, border-color .25s;
        }
        .testimonial-card:hover {
            box-shadow: 0 10px 32px rgba(0,0,0,.07);
            transform: translateY(-4px);
            border-color: #e0e7ff;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: #fff;
            flex-shrink: 0;
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            padding: 5rem 0;
        }

        /* ── Footer ── */
        .footer {
            background: #0f172a;
            color: #64748b;
            font-size: 0.875rem;
            padding: 2rem 0;
        }

        /* ── Keyframe definitions ── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(48px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes orbFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%       { transform: translateY(-28px) scale(1.04); }
        }
        @keyframes orbFadeIn {
            to { opacity: 0.32; }
        }
        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(129,140,248,0); }
            50%       { box-shadow: 0 0 0 7px rgba(129,140,248,.12); }
        }

        /* ── Scroll-reveal ── */
        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.65s ease, transform 0.65s ease;
        }
        .reveal.from-left  { transform: translateX(-32px); }
        .reveal.from-right { transform: translateX(32px); }
        .reveal.scale-up   { transform: scale(0.91) translateY(14px); }
        .reveal.visible    { opacity: 1; transform: none; }

        .delay-1 { transition-delay: .1s; }
        .delay-2 { transition-delay: .2s; }
        .delay-3 { transition-delay: .3s; }
        .delay-4 { transition-delay: .4s; }
        .delay-5 { transition-delay: .5s; }
        .delay-6 { transition-delay: .6s; }

        /* smooth scroll */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>

    {{-- ═══════════════════════════════════════
         NAVBAR
    ═══════════════════════════════════════ --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('renalsight-favicons/favicon-48x48.png') }}" alt="RenalSight" width="36" height="36" style="border-radius:.6rem;">
                <span class="fw-bold text-dark">RenalSight</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto gap-lg-2">
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#how-it-works">How it works</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="#testimonials">Testimonials</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3">Sign in</a>
                    <form method="POST" action="{{ route('guest.login') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-indigo btn-sm px-3">Try for free</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════
         HERO
    ═══════════════════════════════════════ --}}
    <section class="hero">
        {{-- Floating orbs --}}
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>

        <div class="container position-relative">
            <div class="row align-items-center g-5">

                {{-- Left copy --}}
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <i class="bi bi-lightning-charge-fill" style="color:#818cf8"></i>
                        AI-powered review intelligence
                    </div>
                    <h1>Turn Customer Reviews into <span>Actionable Insights</span></h1>
                    <p class="mt-3 mb-4 hero-sub">
                        Automatically analyze sentiment, extract key themes, and track rating trends across all your product reviews — in one clean dashboard.
                    </p>
                    <div class="d-flex flex-wrap gap-3 hero-btns">
                        <a href="{{ route('login') }}" class="btn btn-indigo btn-lg px-4 fw-semibold">
                            Get started <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <form method="POST" action="{{ route('guest.login') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-lg px-4 fw-semibold"
                                style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;">
                                <i class="bi bi-person me-1"></i> Try as Guest
                            </button>
                        </form>
                    </div>
                    <p class="mt-3 hero-note" style="font-size:.8rem;color:#64748b;">
                        No credit card required &nbsp;·&nbsp; Guest access available
                    </p>
                </div>

                {{-- Right mockup --}}
                <div class="col-lg-6">
                    <div class="hero-mockup">
                        <p class="mb-3" style="color:#94a3b8;font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Sentiment Overview</p>

                        <div class="sentiment-bar-wrap">
                            <div class="sentiment-label">Positive</div>
                            <div class="sentiment-bar">
                                <div class="sentiment-bar-fill" data-fill="72%" style="background:linear-gradient(90deg,#22c55e,#4ade80)"></div>
                            </div>
                        </div>
                        <div class="sentiment-bar-wrap mb-4">
                            <div class="sentiment-label">Negative</div>
                            <div class="sentiment-bar">
                                <div class="sentiment-bar-fill" data-fill="28%" style="background:linear-gradient(90deg,#ef4444,#f87171)"></div>
                            </div>
                        </div>

                        <p class="mb-2" style="color:#94a3b8;font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Latest Reviews</p>

                        <div class="review-chip" id="chip1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color:#e2e8f0;font-weight:600;">Wireless Headphones X3</span>
                                <span class="star">★★★★★</span>
                            </div>
                            "Amazing sound quality, battery lasts all day. Best purchase this year!"
                        </div>
                        <div class="review-chip" id="chip2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="color:#e2e8f0;font-weight:600;">Smart Watch Pro</span>
                                <span><span class="star">★★★★</span><span style="color:#475569;font-size:.75rem;">★</span></span>
                            </div>
                            "Great features but the strap is a bit uncomfortable after long wear."
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <div style="flex:1;background:rgba(255,255,255,.05);border-radius:.6rem;padding:.8rem;text-align:center;">
                                <div style="font-size:1.3rem;font-weight:800;color:#4ade80;">72%</div>
                                <div style="font-size:.7rem;color:#64748b;">Positive</div>
                            </div>
                            <div style="flex:1;background:rgba(255,255,255,.05);border-radius:.6rem;padding:.8rem;text-align:center;">
                                <div style="font-size:1.3rem;font-weight:800;color:#f87171;">28%</div>
                                <div style="font-size:.7rem;color:#64748b;">Negative</div>
                            </div>
                            <div style="flex:1;background:rgba(255,255,255,.05);border-radius:.6rem;padding:.8rem;text-align:center;">
                                <div style="font-size:1.3rem;font-weight:800;color:#f8fafc;">1.2k</div>
                                <div style="font-size:.7rem;color:#64748b;">Reviews</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         STATS BAR
    ═══════════════════════════════════════ --}}
    <div class="stats-bar">
        <div class="container">
            <div class="row justify-content-center g-0">
                <div class="col-6 col-sm-3 reveal">
                    <div class="stat-item">
                        <div class="stat-num" data-count="50" data-suffix="k+">50k+</div>
                        <div class="stat-desc">Reviews analyzed</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3 reveal delay-1">
                    <div class="stat-item">
                        <div class="stat-num" data-count="97" data-suffix="%">97%</div>
                        <div class="stat-desc">Sentiment accuracy</div>
                    </div>
                </div>
                <div class="col-6 col-sm-3 reveal delay-2">
                    <div class="stat-item">
                        <div class="stat-num" data-count="500" data-suffix="+">500+</div>
                        <div class="stat-desc">Products tracked</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         FEATURES
    ═══════════════════════════════════════ --}}
    <section id="features" class="py-6" style="padding: 5rem 0;">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <p class="section-label">Features</p>
                <h2 class="fw-bold fs-2 mt-2">Everything you need to understand your customers</h2>
                <p class="text-muted mt-2" style="max-width:520px;margin:auto;">
                    Stop reading reviews one by one. Let our analyzer surface what matters most — automatically.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4 reveal delay-1">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-emoji-smile"></i></div>
                        <h5 class="fw-bold mb-2">Sentiment Analysis</h5>
                        <p class="text-muted small mb-0">
                            Automatically classify each review as positive, neutral, or negative using NLP. See the emotional tone of your product at a glance.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal delay-2">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="fw-bold mb-2">Rating Trends</h5>
                        <p class="text-muted small mb-0">
                            Track how your average rating evolves over time and identify patterns after product updates, campaigns, or seasonal events.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal delay-3">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-tags"></i></div>
                        <h5 class="fw-bold mb-2">Keyword Extraction</h5>
                        <p class="text-muted small mb-0">
                            Discover the most frequently mentioned topics, features, and pain points your customers actually write about.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal delay-1">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-bar-chart-steps"></i></div>
                        <h5 class="fw-bold mb-2">Comparative Analysis</h5>
                        <p class="text-muted small mb-0">
                            Compare multiple products side by side. Understand which items drive praise and which need attention.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal delay-2">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-bell"></i></div>
                        <h5 class="fw-bold mb-2">Review Monitoring</h5>
                        <p class="text-muted small mb-0">
                            Stay on top of incoming reviews in real time. Get alerts when sentiment drops or a spike in negative feedback occurs.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal delay-3">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <h5 class="fw-bold mb-2">Exportable Reports</h5>
                        <p class="text-muted small mb-0">
                            Generate clean, shareable reports for stakeholders with one click. Export as PDF or CSV.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         HOW IT WORKS
    ═══════════════════════════════════════ --}}
    <section id="how-it-works" class="how-section py-5" style="padding: 5rem 0 !important;">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <p class="section-label">How it works</p>
                <h2 class="fw-bold fs-2 mt-2">From raw reviews to clear insights in minutes</h2>
            </div>

            <div class="row justify-content-center g-4">
                <div class="col-md-8 col-lg-6">

                    <div class="d-flex gap-3 align-items-start step-wrap reveal from-left">
                        <div class="d-flex flex-column align-items-center">
                            <div class="step-number">1</div>
                            <div class="step-connector" style="height:48px;"></div>
                        </div>
                        <div class="pb-4">
                            <h6 class="fw-bold mb-1">Add your product</h6>
                            <p class="text-muted small mb-0">Enter your product name and paste in your review data, or connect a source like a CSV export.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-start step-wrap reveal from-left delay-1">
                        <div class="d-flex flex-column align-items-center">
                            <div class="step-number">2</div>
                            <div class="step-connector" style="height:48px;"></div>
                        </div>
                        <div class="pb-4">
                            <h6 class="fw-bold mb-1">Run the analysis</h6>
                            <p class="text-muted small mb-0">Our engine processes each review — scoring sentiment, extracting keywords, and grouping themes automatically.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-start step-wrap reveal from-left delay-2">
                        <div class="d-flex flex-column align-items-center">
                            <div class="step-number">3</div>
                            <div class="step-connector" style="height:48px;"></div>
                        </div>
                        <div class="pb-4">
                            <h6 class="fw-bold mb-1">Explore your dashboard</h6>
                            <p class="text-muted small mb-0">View charts, trends, and keyword clouds that show you exactly what customers love and what needs improvement.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-start step-wrap reveal from-left delay-3">
                        <div class="d-flex flex-column align-items-center">
                            <div class="step-number">4</div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Act on the insights</h6>
                            <p class="text-muted small mb-0">Export reports, share with your team, and track improvements over time as new reviews come in.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         TESTIMONIALS
    ═══════════════════════════════════════ --}}
    <section id="testimonials" style="padding: 5rem 0; background:#fff;">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <p class="section-label">Testimonials</p>
                <h2 class="fw-bold fs-2 mt-2">Loved by product teams</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4 reveal scale-up delay-1">
                    <div class="testimonial-card">
                        <div class="mb-3" style="color:#fbbf24;font-size:.9rem;">★★★★★</div>
                        <p class="text-muted small mb-4">
                            "We used to spend hours manually reading through hundreds of reviews. RenalSight cut that down to minutes and actually found issues we completely missed."
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="background:#4f46e5;">S</div>
                            <div>
                                <div class="fw-semibold small">Sarah K.</div>
                                <div class="text-muted" style="font-size:.75rem;">Product Manager, TechGear Co.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal scale-up delay-2">
                    <div class="testimonial-card">
                        <div class="mb-3" style="color:#fbbf24;font-size:.9rem;">★★★★★</div>
                        <p class="text-muted small mb-4">
                            "The keyword extraction feature alone is worth it. We discovered that 'battery life' appeared in 40% of our negative reviews — something we never would have caught manually."
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="background:#0891b2;">M</div>
                            <div>
                                <div class="fw-semibold small">Marcus T.</div>
                                <div class="text-muted" style="font-size:.75rem;">Head of E-commerce, NovaBrands</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal scale-up delay-3">
                    <div class="testimonial-card">
                        <div class="mb-3" style="color:#fbbf24;font-size:.9rem;">★★★★☆</div>
                        <p class="text-muted small mb-4">
                            "Clean UI, fast results, and the sentiment accuracy is impressive. The export feature makes it easy to include in our monthly stakeholder reports."
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="background:#16a34a;">A</div>
                            <div>
                                <div class="fw-semibold small">Aisha R.</div>
                                <div class="text-muted" style="font-size:.75rem;">Data Analyst, RetailHub</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         CTA
    ═══════════════════════════════════════ --}}
    <section class="cta-section text-center">
        <div class="container reveal" style="max-width:600px;">
            <h2 class="fw-bold text-white fs-2 mb-3">Ready to understand your customers?</h2>
            <p class="text-white-50 mb-4">
                Jump straight into the dashboard — no setup required. Start with a free guest session or create an account.
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <a href="{{ route('login') }}" class="btn btn-indigo btn-lg px-4 fw-semibold">
                    Sign in <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <form method="POST" action="{{ route('guest.login') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-lg px-4 fw-semibold"
                        style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.25);color:#fff;">
                        <i class="bi bi-person me-1"></i> Try as Guest
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════ --}}
    <footer class="footer">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('renalsight-favicons/favicon-32x32.png') }}" alt="RenalSight" width="26" height="26" style="border-radius:.4rem;">
                <span style="color:#475569;font-size:.8rem;">RenalSight</span>
            </div>
            <p class="mb-0" style="font-size:.8rem;">&copy; {{ date('Y') }} RenalSight. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* ── Navbar scroll shadow ── */
        window.addEventListener('scroll', () => {
            document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 10);
        });

        /* ── Sentiment bars: animate on load ── */
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.querySelectorAll('.sentiment-bar-fill[data-fill]').forEach(el => {
                    el.style.width = el.dataset.fill;
                });
            }, 500);

            setTimeout(() => {
                document.getElementById('chip1')?.classList.add('chip-visible');
            }, 900);
            setTimeout(() => {
                document.getElementById('chip2')?.classList.add('chip-visible');
            }, 1150);
        });

        /* ── Scroll reveal via IntersectionObserver ── */
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        /* ── Count-up animation for stat numbers ── */
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target  = parseFloat(el.dataset.count);
                const suffix  = el.dataset.suffix ?? '';
                const duration = 1600;
                let startTime = null;

                const step = (ts) => {
                    if (!startTime) startTime = ts;
                    const progress = Math.min((ts - startTime) / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(ease * target) + suffix;
                    if (progress < 1) requestAnimationFrame(step);
                };

                requestAnimationFrame(step);
                countObserver.unobserve(el);
            });
        }, { threshold: 0.6 });

        document.querySelectorAll('[data-count]').forEach(el => countObserver.observe(el));
    </script>
</body>
</html>
