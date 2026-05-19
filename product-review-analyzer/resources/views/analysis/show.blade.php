<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $analysis->product_name }} — Product Review Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            font-size: .72rem; font-weight: 700;
            letter-spacing: .07em; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 1rem;
        }

        /* Stat tiles */
        .stat-tile .num { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-tile .lbl { font-size: .75rem; color: #64748b; margin-top: .2rem; }

        /* Sentiment bars */
        .sent-bar { height: 10px; border-radius: 999px; background: #f1f5f9; overflow: hidden; }
        .sent-bar-fill { height: 100%; border-radius: 999px; }

        /* Negative reasons */
        .reason-card {
            display: flex; align-items: flex-start; gap: 1rem;
            border: 1px solid #fee2e2; border-radius: .75rem;
            background: #fff5f5; padding: 1rem 1.1rem;
        }
        .reason-rank {
            width: 32px; height: 32px; border-radius: 999px;
            background: #ef4444; color: #fff;
            font-weight: 800; font-size: .85rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .reason-rank.rank-2 { background: #f97316; }
        .reason-rank.rank-3 { background: #f59e0b; }
        .reason-text { font-weight: 600; font-size: .9rem; color: #1e293b; line-height: 1.4; }
        .reason-count { font-size: .78rem; color: #ef4444; font-weight: 600; margin-top: .2rem; }

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

        @media print {
            .navbar, .btn, form, .filter-tabs { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
    <div class="container-lg">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <div class="navbar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                </svg>
            </div>
            <span class="fw-bold text-dark small">ReviewAnalyzer</span>
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Print
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

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $analysis->product_name }}</h1>
            <span class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>Analyzed on {{ $analysis->created_at->format('d M Y, H:i') }}
            </span>
        </div>
        @php
            $verdict = $analysis->positive_count >= $analysis->negative_count ? 'Mostly Positive' : 'Mostly Negative';
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
                    <div class="num" style="color:#16a34a;">{{ $analysis->positivePercent() }}<span style="font-size:1rem;">%</span></div>
                    <div class="lbl">Positive</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="section-card text-center">
                <div class="stat-tile">
                    <div class="num" style="color:#dc2626;">{{ $analysis->negativePercent() }}<span style="font-size:1rem;">%</span></div>
                    <div class="lbl">Negative</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Top 3 Negative Reasons + Sentiment bars ── --}}
    <div class="row g-3 mb-4">

        {{-- Top 3 Negative Reasons --}}
        <div class="col-lg-7">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-exclamation-triangle-fill me-1" style="color:#ef4444;"></i>
                    Top 3 Negative Sentiment Reasons
                </p>

                @if (!empty($analysis->top_negative_reasons))
                    <div class="d-flex flex-column gap-3">
                        @foreach ($analysis->top_negative_reasons as $i => $reason)
                        <div class="reason-card">
                            <div class="reason-rank {{ $i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '') }}">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <div class="reason-text">{{ $reason['reason'] }}</div>
                                @if (!empty($reason['count']))
                                    <div class="reason-count">
                                        <i class="bi bi-chat-left-text-fill me-1"></i>
                                        Mentioned in {{ $reason['count'] }} {{ Str::plural('review', $reason['count']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0">No negative reasons returned by the API.</p>
                @endif
            </div>
        </div>

        {{-- Sentiment breakdown --}}
        <div class="col-lg-5">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-bar-chart-fill me-1"></i>Sentiment Breakdown
                </p>

                <div class="mb-4">
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

                <div class="mb-1">
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

    </div>

    {{-- ── Reviews list ── --}}
    <div class="section-card shadow-sm" style="padding: 0; overflow: hidden;">
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

        @if (!empty($analysis->reviews_data))
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reviewsTable">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Review</th>
                            <th style="width:120px;" class="text-center">Sentiment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analysis->reviews_data as $i => $review)
                        <tr data-sentiment="{{ $review['label'] ?? 'unknown' }}">
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td class="small">{{ $review['text'] }}</td>
                            <td class="text-center">
                                @if (($review['label'] ?? '') === 'positive')
                                    <span class="badge rounded-pill badge-pos px-3">
                                        <i class="bi bi-emoji-smile-fill me-1"></i>Positive
                                    </span>
                                @else
                                    <span class="badge rounded-pill badge-neg px-3">
                                        <i class="bi bi-emoji-frown-fill me-1"></i>Negative
                                    </span>
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
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Filter reviews table by sentiment
    document.querySelectorAll('#reviewFilter .nav-link').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#reviewFilter .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            document.querySelectorAll('#reviewsTable tbody tr').forEach(row => {
                const sentiment = row.dataset.sentiment;
                row.style.display = (filter === 'all' || sentiment === filter) ? '' : 'none';
            });
        });
    });
</script>
</body>
</html>
