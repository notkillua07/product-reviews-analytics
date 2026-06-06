<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $analysis->product_name }} — RenalSight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('partials.favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .navbar-brand-icon {
            width: 34px; height: 34px; background: #4f46e5; border-radius: .55rem;
            display: flex; align-items: center; justify-content: center;
        }
        .section-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 1rem; padding: 1.5rem;
        }
        .section-title {
            font-size: .7rem; font-weight: 700;
            letter-spacing: .07em; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 1rem;
            display: flex; align-items: center; gap: .4rem;
        }

        /* Stat tiles */
        .stat-tile .num { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-tile .lbl { font-size: .75rem; color: #64748b; margin-top: .2rem; }

        /* Sentiment bars */
        .sent-bar { height: 8px; border-radius: 999px; background: #f1f5f9; overflow: hidden; }
        .sent-bar-fill { height: 100%; border-radius: 999px; }

        /* Reason cards */
        .reason-card {
            display: flex; align-items: flex-start; gap: .85rem;
            border-radius: .65rem; padding: .85rem 1rem;
            margin-bottom: .6rem;
        }
        .reason-card:last-child { margin-bottom: 0; }

        /* Product reasons — red → orange → yellow gradation */
        .reason-card.product-reason { border: 1px solid #fee2e2; background: #fff5f5; }
        .product-reason .reason-rank      { background: #dc2626; }          /* rank 1 — deep red     */
        .product-reason .reason-rank.r2   { background: #f97316; }          /* rank 2 — orange       */
        .product-reason .reason-rank.r3   { background: #eab308; }          /* rank 3 — yellow       */
        .product-reason .reason-rank.rest { background: #94a3b8; }          /* rank 4+ — slate grey  */
        .product-reason .reason-count { color: #ef4444; }

        /* Shipping reasons — dark amber → mid amber → light amber gradation */
        .reason-card.shipping-reason { border: 1px solid #fde68a; background: #fffbeb; }
        .shipping-reason .reason-rank      { background: #b45309; }          /* rank 1 — dark amber   */
        .shipping-reason .reason-rank.r2   { background: #d97706; }          /* rank 2 — mid amber    */
        .shipping-reason .reason-rank.r3   { background: #fbbf24; color: #78350f; } /* rank 3 — light amber */
        .shipping-reason .reason-rank.rest { background: #94a3b8; color: #fff; }    /* rank 4+ — slate grey */
        .shipping-reason .reason-count { color: #d97706; }

        .reason-rank {
            width: 28px; height: 28px; border-radius: 999px;
            color: #fff; font-weight: 800; font-size: .8rem;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .reason-text { font-weight: 600; font-size: .87rem; color: #1e293b; line-height: 1.4; }
        .reason-count { font-size: .74rem; font-weight: 600; margin-top: .2rem; }

        /* Reviews table */
        .table > :not(caption) > * > * { padding: .8rem 1rem; }
        .table thead th {
            font-size: .72rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            color: #94a3b8; background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .table tbody tr:last-child td { border-bottom: 0; }
        .badge-pos { background: #dcfce7; color: #15803d; }
        .badge-neg { background: #fee2e2; color: #b91c1c; }

        /* Filter tabs */
        .filter-tabs .nav-link {
            color: #64748b; font-size: .82rem; font-weight: 600;
            padding: .35rem .85rem; border-radius: 999px;
        }
        .filter-tabs .nav-link.active { background: #4f46e5; color: #fff; }
        .filter-tabs .nav-link:not(.active):hover { background: #f1f5f9; }

        /* Empty reason state */
        .reason-empty { color: #94a3b8; font-size: .82rem; }

        /* Severity badges */
        .sev-badge {
            display: inline-flex; align-items: center; gap: .25rem;
            font-size: .65rem; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; padding: .18rem .55rem; border-radius: 999px;
            cursor: pointer; user-select: none;
        }
        .sev-badge .sev-chevron { transition: transform .2s; font-style: normal; font-size: .6rem; margin-left: .1rem; }
        .sev-badge.open .sev-chevron { transform: rotate(180deg); }
        .sev-critical { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .sev-moderate { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .sev-minor    { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        /* Severity explanation accordion panel */
        .sev-explanation {
            display: none; margin-top: .45rem;
            font-size: .75rem; line-height: 1.5; color: #475569;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: .45rem; padding: .45rem .65rem;
        }
        .sev-explanation.open { display: block; }

        /* Scrollable reasons list */
        .reasons-scroll { max-height: 420px; overflow-y: auto; padding-right: 2px; }
        .reasons-scroll::-webkit-scrollbar { width: 4px; }
        .reasons-scroll::-webkit-scrollbar-track { background: transparent; }
        .reasons-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .reasons-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Review IDs filter button */
        .review-ids-label {
            font-size: .66rem; color: #94a3b8; font-weight: 700;
            letter-spacing: .05em; text-transform: uppercase;
            display: block; margin-top: .55rem; margin-bottom: .3rem;
        }
        .review-ids-btn {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .71rem; font-weight: 600;
            padding: .22rem .7rem; border-radius: 999px;
            border: 1.5px dashed #a5b4fc; background: #f5f3ff;
            color: #4f46e5; cursor: pointer; transition: all .15s ease;
        }
        .review-ids-btn:hover { background: #e0e7ff; border-color: #4f46e5; border-style: solid; }
        .review-ids-btn.active-id-filter { background: #4f46e5; color: #fff; border-color: #4f46e5; border-style: solid; }

        /* Active filter banner */
        #activeIdFilterBanner { background: #eef2ff; }


        /* Confidence */
        .conf-high   { color: #15803d; font-weight: 600; }
        .conf-medium { color: #d97706; font-weight: 600; }
        .conf-low    { color: #dc2626; font-weight: 600; }
        .conf-label  { font-size: .68rem; color: #94a3b8; display: block; }

        /* Pagination */
        .pagination .page-link { font-size: .8rem; padding: .3rem .6rem; color: #4f46e5; }
        .pagination .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
        .pagination .page-item.disabled .page-link { color: #cbd5e1; }

        @media print {
            .navbar, .btn, form, .filter-tabs, #paginationContainer { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
    <div class="container-lg">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('renalsight-favicons/favicon-48x48.png') }}" alt="RenalSight" width="34" height="34" style="border-radius:.55rem;">
            <span class="fw-bold text-dark small">RenalSight</span>
        </a>
        <div class="d-flex gap-2">
            <button onclick="exportPDF()" id="exportBtn" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
            <form method="POST" action="{{ route('analysis.destroy', $analysis) }}"
                  onsubmit="return confirm('Delete this analysis permanently?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ── Main ── --}}
<main class="container-lg py-5">

    <a href="{{ route('home') }}" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 mb-4">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    <div id="pdf-summary">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $analysis->product_name }}</h1>
            <span class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>Analyzed on {{ $analysis->created_at->format('d M Y, H:i') }}
                @if ($analysis->product?->category)
                    &nbsp;·&nbsp;
                    <span class="badge" style="background:#ede9fe;color:#6d28d9;font-size:.72rem;">
                        {{ $analysis->product->category->name }}
                    </span>
                @endif
            </span>
        </div>
        @php
            $verdict      = $analysis->positive_count >= $analysis->negative_count ? 'Mostly Positive' : 'Mostly Negative';
            $verdictClass = $analysis->positive_count >= $analysis->negative_count ? 'badge-pos' : 'badge-neg';
        @endphp
        <span class="badge rounded-pill px-3 py-2 {{ $verdictClass }}" style="font-size:.85rem;">
            {{ $verdict }}
        </span>
    </div>

    {{-- ── Stat tiles ── --}}
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="section-card text-center">
                <div class="stat-tile">
                    <div class="num text-dark">{{ $analysis->total_reviews }}</div>
                    <div class="lbl">Total Reviews</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="section-card text-center">
                <div class="stat-tile">
                    <div class="num" style="color:#16a34a;">
                        {{ $analysis->positivePercent() }}<span style="font-size:1rem;">%</span>
                    </div>
                    <div class="lbl">Positive</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="section-card text-center">
                <div class="stat-tile">
                    <div class="num" style="color:#dc2626;">
                        {{ $analysis->negativePercent() }}<span style="font-size:1rem;">%</span>
                    </div>
                    <div class="lbl">Negative</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sentiment Breakdown ── --}}
    <div class="section-card mb-4">
        <p class="section-title">
            <i class="bi bi-bar-chart-fill"></i>Sentiment Breakdown
        </p>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-semibold" style="color:#15803d;">
                        <i class="bi bi-emoji-smile-fill me-1"></i>Positive
                    </span>
                    <span class="fw-bold">
                        {{ $analysis->positive_count }}
                        <span class="text-muted fw-normal">({{ $analysis->positivePercent() }}%)</span>
                    </span>
                </div>
                <div class="sent-bar">
                    <div class="sent-bar-fill" style="width:{{ $analysis->positivePercent() }}%; background:#22c55e;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-semibold" style="color:#dc2626;">
                        <i class="bi bi-emoji-frown-fill me-1"></i>Negative
                    </span>
                    <span class="fw-bold">
                        {{ $analysis->negative_count }}
                        <span class="text-muted fw-normal">({{ $analysis->negativePercent() }}%)</span>
                    </span>
                </div>
                <div class="sent-bar">
                    <div class="sent-bar-fill" style="width:{{ $analysis->negativePercent() }}%; background:#ef4444;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Negative Reason Categories ── --}}
    <div class="row g-3 mb-4">

        {{-- Product Issues --}}
        <div class="col-lg-6">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-box-seam-fill" style="color:#ef4444;"></i>
                    Product Issues
                </p>

                <div class="reasons-scroll">
                @forelse ($analysis->productReasons as $i => $reason)
                    @php $rankClass = match(true) { $i === 1 => 'r2', $i === 2 => 'r3', $i > 2 => 'rest', default => '' }; @endphp
                    <div class="reason-card product-reason">
                        <div class="reason-rank {{ $rankClass }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="reason-text">{{ $reason->reason }}</div>
                                @if ($reason->severity)
                                    @php $sevId = 'sev-p-' . $loop->index; @endphp
                                    <span class="sev-badge sev-{{ $reason->severity }}"
                                          @if($reason->severity_explanation) onclick="toggleSev('{{ $sevId }}')" @endif>
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        {{ $reason->severity }}
                                        @if ($reason->severity_score) · {{ $reason->severity_score }}/5 @endif
                                        @if($reason->severity_explanation)<i class="sev-chevron">▾</i>@endif
                                    </span>
                                @endif
                            </div>
                            @if ($reason->severity_explanation)
                                <div class="sev-explanation" id="sev-p-{{ $loop->index }}">{{ $reason->severity_explanation }}</div>
                            @endif
                            @if ($reason->count)
                                <div class="reason-count mt-1">
                                    <i class="bi bi-chat-left-text-fill me-1"></i>
                                    {{ $reason->count }} {{ Str::plural('mention', $reason->count) }}
                                </div>
                            @endif
                            @if (!empty($reason->review_ids))
                                <span class="review-ids-label">
                                    <i class="bi bi-funnel-fill me-1"></i>Filter affected reviews
                                </span>
                                <button class="review-ids-btn"
                                    onclick="filterByReviewIds({{ json_encode($reason->review_ids) }}, this)">
                                    <i class="bi bi-funnel me-1"></i>
                                    {{ count($reason->review_ids) }} {{ Str::plural('review', count($reason->review_ids)) }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="reason-empty mb-0">No product-related issues found.</p>
                @endforelse
                </div>
            </div>
        </div>

        {{-- Shipping Issues --}}
        <div class="col-lg-6">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-truck" style="color:#d97706;"></i>
                    Shipping Issues
                </p>

                <div class="reasons-scroll">
                @forelse ($analysis->shippingReasons as $i => $reason)
                    @php $rankClass = match(true) { $i === 1 => 'r2', $i === 2 => 'r3', $i > 2 => 'rest', default => '' }; @endphp
                    <div class="reason-card shipping-reason">
                        <div class="reason-rank {{ $rankClass }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="reason-text">{{ $reason->reason }}</div>
                                @if ($reason->severity)
                                    @php $sevId = 'sev-s-' . $loop->index; @endphp
                                    <span class="sev-badge sev-{{ $reason->severity }}"
                                          @if($reason->severity_explanation) onclick="toggleSev('{{ $sevId }}')" @endif>
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        {{ $reason->severity }}
                                        @if ($reason->severity_score) · {{ $reason->severity_score }}/5 @endif
                                        @if($reason->severity_explanation)<i class="sev-chevron">▾</i>@endif
                                    </span>
                                @endif
                            </div>
                            @if ($reason->severity_explanation)
                                <div class="sev-explanation" id="sev-s-{{ $loop->index }}">{{ $reason->severity_explanation }}</div>
                            @endif
                            @if ($reason->count)
                                <div class="reason-count mt-1">
                                    <i class="bi bi-chat-left-text-fill me-1"></i>
                                    {{ $reason->count }} {{ Str::plural('mention', $reason->count) }}
                                </div>
                            @endif
                            @if (!empty($reason->review_ids))
                                <span class="review-ids-label">
                                    <i class="bi bi-funnel-fill me-1"></i>Filter affected reviews
                                </span>
                                <button class="review-ids-btn"
                                    onclick="filterByReviewIds({{ json_encode($reason->review_ids) }}, this)">
                                    <i class="bi bi-funnel me-1"></i>
                                    {{ count($reason->review_ids) }} {{ Str::plural('review', count($reason->review_ids)) }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="reason-empty mb-0">No shipping-related issues found.</p>
                @endforelse
                </div>
            </div>
        </div>

    </div>

    </div>{{-- /pdf-summary --}}

    {{-- ── Reviews list ── --}}
    <div id="reviewsSection" class="section-card shadow-sm" style="padding: 0; overflow: hidden;">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2"
             style="background:#f8fafc;">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-chat-square-text me-2 text-muted"></i>All Reviews
                <span class="badge bg-light text-secondary border ms-2">{{ $analysis->total_reviews }}</span>
            </h6>

            {{-- Filter tabs --}}
            <div class="filter-tabs">
                <ul class="nav gap-1" id="reviewFilter">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-filter="all">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter="positive">
                            <i class="bi bi-emoji-smile me-1"></i>Positive
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter="negative">
                            <i class="bi bi-emoji-frown me-1"></i>Negative
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Active review-ID filter banner --}}
        <div id="activeIdFilterBanner" class="px-4 py-2 border-bottom d-none d-flex align-items-center gap-3">
            <span class="small fw-semibold" style="color:#4338ca;">
                <i class="bi bi-funnel-fill me-1"></i>
                Showing <span id="activeIdCount"></span> reviews linked to this reason
            </span>
            <button onclick="clearIdFilter()"
                    class="btn btn-link btn-sm text-danger p-0 text-decoration-none small">
                <i class="bi bi-x-circle me-1"></i>Clear filter
            </button>
        </div>

        @if ($analysis->reviews->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reviewsTable">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Review</th>
                            <th style="width:120px;" class="text-center">Sentiment</th>
                            <th style="width:110px;" class="text-center">Confidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analysis->reviews as $review)
                        <tr data-sentiment="{{ $review->label }}">
                            <td class="text-muted small">{{ $review->review_order_id }}</td>
                            <td class="small">{{ $review->text }}</td>
                            <td class="text-center">
                                @if ($review->label === 'positive')
                                    <span class="badge rounded-pill badge-pos px-3">
                                        <i class="bi bi-emoji-smile-fill me-1"></i>Positive
                                    </span>
                                @else
                                    <span class="badge rounded-pill badge-neg px-3">
                                        <i class="bi bi-emoji-frown-fill me-1"></i>Negative
                                    </span>
                                @endif
                            </td>
                            <td class="text-center small">
                                @if ($review->confidence !== null)
                                    <span class="conf-{{ $review->confidence_level ?? 'high' }}">
                                        {{ $review->confidencePercent() }}
                                    </span>
                                    <span class="conf-label">{{ ucfirst($review->confidence_level ?? '') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted small p-4 mb-0">No review data available.</p>
        @endif

        {{-- Pagination bar --}}
        @if ($analysis->reviews->isNotEmpty())
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2"
             id="paginationContainer" style="background:#f8fafc;">
            <span class="text-muted small" id="paginationInfo"></span>
            <nav aria-label="Reviews pagination">
                <ul class="pagination pagination-sm mb-0" id="reviewsPagination"></ul>
            </nav>
        </div>
        @endif
    </div>

</main>

@php
    $pdfProductReasons  = $analysis->productReasons->values()->map(fn($r) => [
        'reason'              => $r->reason,
        'severity'            => $r->severity,
        'severityScore'       => $r->severity_score,
        'severityExplanation' => $r->severity_explanation,
        'count'               => $r->count,
    ]);
    $pdfShippingReasons = $analysis->shippingReasons->values()->map(fn($r) => [
        'reason'              => $r->reason,
        'severity'            => $r->severity,
        'severityScore'       => $r->severity_score,
        'severityExplanation' => $r->severity_explanation,
        'count'               => $r->count,
    ]);
@endphp
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    const AD = {
        productName:     @json($analysis->product_name),
        category:        @json($analysis->product->category?->name ?? null),
        date:            @json($analysis->created_at->format('d M Y, H:i')),
        positiveCount:   {{ $analysis->positive_count }},
        negativeCount:   {{ $analysis->negative_count }},
        positivePercent: {{ $analysis->positivePercent() }},
        negativePercent: {{ $analysis->negativePercent() }},
        productReasons:  @json($pdfProductReasons),
        shippingReasons: @json($pdfShippingReasons),
    };

    const ROWS_PER_PAGE = 15;
    let currentPage   = 1;
    let currentFilter = 'all';
    let reviewIdFilter = null; // null = no ID filter, or array of review_order_ids

    function allRows() {
        return Array.from(document.querySelectorAll('#reviewsTable tbody tr'));
    }

    function filteredRows() {
        return allRows().filter(row => {
            const sentimentOk = currentFilter === 'all' || row.dataset.sentiment === currentFilter;
            const idOk = reviewIdFilter === null ||
                reviewIdFilter.includes(parseInt(row.cells[0].textContent.trim()));
            return sentimentOk && idOk;
        });
    }

    function filterByReviewIds(ids, btn) {
        const alreadyActive = btn.classList.contains('active-id-filter');
        document.querySelectorAll('.review-ids-btn').forEach(b => b.classList.remove('active-id-filter'));

        if (alreadyActive) {
            reviewIdFilter = null;
        } else {
            reviewIdFilter = ids;
            btn.classList.add('active-id-filter');
            document.getElementById('reviewsSection')
                .scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        currentPage = 1;
        renderTable();
    }

    function clearIdFilter() {
        reviewIdFilter = null;
        document.querySelectorAll('.review-ids-btn').forEach(b => b.classList.remove('active-id-filter'));
        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const rows   = filteredRows();
        const total  = rows.length;
        const pages  = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));

        if (currentPage > pages) currentPage = pages;

        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end   = start + ROWS_PER_PAGE;

        // Hide all, then show current page of filtered rows
        allRows().forEach(r => r.style.display = 'none');
        rows.slice(start, end).forEach(r => r.style.display = '');

        // Active ID filter banner
        const banner = document.getElementById('activeIdFilterBanner');
        if (banner) {
            if (reviewIdFilter !== null) {
                banner.classList.remove('d-none');
                document.getElementById('activeIdCount').textContent = reviewIdFilter.length;
            } else {
                banner.classList.add('d-none');
            }
        }

        // Info text
        const info = document.getElementById('paginationInfo');
        if (info) {
            info.textContent = total === 0
                ? 'No reviews'
                : `Showing ${start + 1}–${Math.min(end, total)} of ${total} reviews`;
        }

        // Build pagination buttons
        const nav = document.getElementById('reviewsPagination');
        if (!nav) return;
        nav.innerHTML = '';

        const mkLi = (label, page, disabled, active) => {
            const li = document.createElement('li');
            li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = label;
            if (!disabled && !active) {
                a.addEventListener('click', e => { e.preventDefault(); currentPage = page; renderTable(); });
            }
            li.appendChild(a);
            return li;
        };

        nav.appendChild(mkLi('&laquo;', currentPage - 1, currentPage === 1, false));

        // Show at most 5 page buttons centred around current page
        let from = Math.max(1, currentPage - 2);
        let to   = Math.min(pages, from + 4);
        from     = Math.max(1, to - 4);

        for (let p = from; p <= to; p++) {
            nav.appendChild(mkLi(p, p, false, p === currentPage));
        }

        nav.appendChild(mkLi('&raquo;', currentPage + 1, currentPage === pages, false));
    }

    // Filter tabs
    document.querySelectorAll('#reviewFilter .nav-link').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#reviewFilter .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            currentPage   = 1;
            renderTable();
        });
    });

    // PDF export — fully text-based, no screenshots
    async function exportPDF() {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating…';

        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ unit: 'mm', format: 'a4' });
            const M   = 14;         // margin
            const PW  = 210;        // page width
            const PH  = 297;        // page height
            const UW  = PW - M * 2; // usable width
            let y = M;

            const C = {
                brand:    [79,  70,  229],
                dark:     [30,  41,  59],
                mid:      [71,  85,  105],
                muted:    [100, 116, 139],
                light:    [148, 163, 184],
                pos:      [21,  128, 61],
                neg:      [185, 28,  28],
                critical: [153, 27,  27],
                moderate: [146, 64,  14],
                minor:    [22,  101, 52],
                bg:       [248, 250, 252],
                border:   [226, 232, 240],
            };

            function sf(size, weight = 'normal', color = C.dark) {
                doc.setFontSize(size);
                doc.setFont('helvetica', weight);
                doc.setTextColor(...color);
            }

            function hRule(yPos) {
                doc.setDrawColor(...C.border);
                doc.setLineWidth(0.2);
                doc.line(M, yPos, M + UW, yPos);
            }

            function checkPage(needed = 10) {
                if (y + needed > PH - M - 10) { doc.addPage(); y = M; }
            }

            // ── BRAND HEADER BAR ──
            doc.setFillColor(...C.brand);
            doc.rect(0, 0, PW, 14, 'F');
            sf(11, 'bold', [255, 255, 255]);
            doc.text('RenalSight', M, 9.5);
            sf(8, 'normal', [200, 210, 255]);
            doc.text('Product Analysis Report', M + 32, 9.5);
            y = 22;

            // ── PRODUCT META ──
            sf(15, 'bold');
            doc.text(AD.productName, M, y);
            y += 6;
            if (AD.category) {
                sf(9, 'normal', C.muted);
                doc.text(AD.category, M, y);
                y += 5;
            }
            sf(8, 'normal', C.muted);
            doc.text(`Generated: ${AD.date}  ·  Total Reviews: ${AD.positiveCount + AD.negativeCount}`, M, y);
            y += 8;
            hRule(y); y += 6;

            // ── SENTIMENT OVERVIEW ──
            sf(8, 'bold', C.muted);
            doc.text('SENTIMENT OVERVIEW', M, y);
            y += 5;

            const barW = UW * 0.50;
            const barH = 4.5;
            const stX  = M + barW + 5;

            // Positive bar
            const posBarW = barW * (AD.positivePercent / 100);
            doc.setFillColor(...C.border);
            doc.roundedRect(M, y, barW, barH, 1, 1, 'F');
            if (posBarW > 1) { doc.setFillColor(...C.pos); doc.roundedRect(M, y, posBarW, barH, 1, 1, 'F'); }
            sf(9, 'bold', C.pos);
            const posLabel = `${AD.positivePercent}%`;
            doc.text(posLabel, stX, y + 3.3);
            sf(8, 'normal', C.mid);
            doc.text(`  Positive  (${AD.positiveCount})`, stX + doc.getTextWidth(posLabel), y + 3.3);
            y += barH + 2.5;

            // Negative bar
            const negBarW = barW * (AD.negativePercent / 100);
            doc.setFillColor(...C.border);
            doc.roundedRect(M, y, barW, barH, 1, 1, 'F');
            if (negBarW > 1) { doc.setFillColor(...C.neg); doc.roundedRect(M, y, negBarW, barH, 1, 1, 'F'); }
            sf(9, 'bold', C.neg);
            const negLabel = `${AD.negativePercent}%`;
            doc.text(negLabel, stX, y + 3.3);
            sf(8, 'normal', C.mid);
            doc.text(`  Negative  (${AD.negativeCount})`, stX + doc.getTextWidth(negLabel), y + 3.3);
            y += barH + 8;
            hRule(y); y += 6;

            // ── REASONS RENDERER ──
            function drawReasons(title, reasons, rankColors) {
                checkPage(20);
                sf(8, 'bold', C.muted);
                doc.text(title.toUpperCase(), M, y);
                y += 5;

                if (!reasons || !reasons.length) {
                    sf(8, 'normal', C.light);
                    doc.text('No issues found.', M + 4, y);
                    y += 7;
                    return;
                }

                reasons.forEach((r, idx) => {
                    const rankColor = rankColors[idx] || C.light;
                    const textX = M + 9;
                    const textW = UW - 9;

                    const reasonLines = doc.splitTextToSize(r.reason, textW);
                    const expLines    = r.severityExplanation
                        ? doc.splitTextToSize(r.severityExplanation, textW - 5)
                        : [];
                    const blockH = reasonLines.length * 4.5
                                 + (r.severity ? 4.5 : 0)
                                 + (expLines.length ? expLines.length * 3.8 + 5 : 0)
                                 + 5;
                    checkPage(blockH);

                    // Rank circle
                    doc.setFillColor(...rankColor);
                    doc.circle(M + 3, y + 2, 2.8, 'F');
                    doc.setFontSize(7); doc.setFont('helvetica', 'bold'); doc.setTextColor(255, 255, 255);
                    doc.text(String(idx + 1), M + 3, y + 3, { align: 'center' });

                    // Reason text
                    sf(9, 'normal', C.dark);
                    doc.text(reasonLines, textX, y + 1.5);
                    y += reasonLines.length * 4.5;

                    // Severity + count
                    if (r.severity) {
                        const sevColor = r.severity === 'critical' ? C.critical
                                       : r.severity === 'moderate' ? C.moderate : C.minor;
                        const sevStr   = `${r.severity.toUpperCase()}${r.severityScore ? '  ' + r.severityScore + '/5' : ''}`;
                        sf(7, 'bold', sevColor);
                        doc.text(sevStr, textX, y + 1.5);
                        if (r.count) {
                            sf(7, 'normal', C.muted);
                            doc.text(`  ·  ${r.count} mention${r.count !== 1 ? 's' : ''}`, textX + doc.getTextWidth(sevStr), y + 1.5);
                        }
                        y += 4.5;
                    } else if (r.count) {
                        sf(7, 'normal', C.muted);
                        doc.text(`${r.count} mention${r.count !== 1 ? 's' : ''}`, textX, y + 1.5);
                        y += 4.5;
                    }

                    // Severity explanation box
                    if (expLines.length) {
                        const boxH = expLines.length * 3.8 + 3.5;
                        checkPage(boxH + 4);
                        doc.setFillColor(...C.bg);
                        doc.setDrawColor(...C.border);
                        doc.setLineWidth(0.15);
                        doc.roundedRect(textX, y, textW - 2, boxH, 1.5, 1.5, 'FD');
                        sf(7, 'normal', C.mid);
                        doc.text(expLines, textX + 2.5, y + 3);
                        y += boxH + 2;
                    }

                    y += 3;
                });
            }

            const prodColors = [[220,38,38],[249,115,22],[234,179,8],...Array(20).fill(C.light)];
            const shipColors = [[180,83,9],[217,119,6],[251,191,36],...Array(20).fill(C.light)];

            drawReasons('Product Issues', AD.productReasons, prodColors);
            checkPage(12); hRule(y); y += 6;
            drawReasons('Shipping Issues', AD.shippingReasons, shipColors);

            // ── FOOTERS on summary pages ──
            const summaryPages = doc.internal.getNumberOfPages();
            for (let i = 1; i <= summaryPages; i++) {
                doc.setPage(i);
                hRule(PH - 9);
                sf(7, 'normal', C.light);
                doc.text('RenalSight', M, PH - 5.5);
                doc.text(`Page ${i}`, M + UW, PH - 5.5, { align: 'right' });
            }

            // ── REVIEWS TABLE ──
            doc.addPage();
            sf(11, 'bold', C.dark);
            doc.text('ALL REVIEWS', M, M + 5);

            const tableRows = allRows().map(row => [
                row.cells[0].textContent.trim(),
                row.cells[1].textContent.trim(),
                row.dataset.sentiment === 'positive' ? 'Positive' : 'Negative',
                row.cells[3] ? row.cells[3].textContent.replace(/\s+/g, ' ').trim() : '—',
            ]);

            doc.autoTable({
                startY: M + 10,
                head:   [['#', 'Review', 'Sentiment', 'Confidence']],
                body:   tableRows,
                margin: { left: M, right: M },
                styles:      { fontSize: 8, cellPadding: 2.5, overflow: 'linebreak' },
                headStyles:  { fillColor: C.brand, textColor: 255, fontStyle: 'bold' },
                columnStyles: {
                    0: { cellWidth: 10, halign: 'center' },
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 24, halign: 'center' },
                    3: { cellWidth: 24, halign: 'center' },
                },
                didParseCell(data) {
                    if (data.section === 'body' && data.column.index === 2) {
                        data.cell.styles.textColor = data.cell.raw === 'Positive' ? C.pos : C.neg;
                        data.cell.styles.fontStyle = 'bold';
                    }
                },
                didDrawPage() {
                    const pg = doc.internal.getCurrentPageInfo().pageNumber;
                    doc.setDrawColor(...C.border); doc.setLineWidth(0.2);
                    doc.line(M, PH - 9, M + UW, PH - 9);
                    doc.setFontSize(7); doc.setFont('helvetica', 'normal'); doc.setTextColor(...C.light);
                    doc.text('RenalSight', M, PH - 5.5);
                    doc.text(`Page ${pg}`, M + UW, PH - 5.5, { align: 'right' });
                },
            });

            doc.save('{{ Str::slug($analysis->product_name) }}_analysis.pdf');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Export PDF';
        }
    }

    // Severity explanation accordion toggle
    function toggleSev(id) {
        const panel = document.getElementById(id);
        const badge = panel.closest('.flex-grow-1').querySelector('.sev-badge');
        panel.classList.toggle('open');
        if (badge) badge.classList.toggle('open');
    }

    // Init
    renderTable();
</script>
</body>
</html>
