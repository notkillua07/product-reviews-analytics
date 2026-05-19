<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Product Review Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }

        .navbar-brand-icon {
            width: 34px; height: 34px; background: #4f46e5; border-radius: .55rem;
            display: flex; align-items: center; justify-content: center;
        }

        /* Stat cards */
        .stat-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem;
            padding: 1.4rem 1.6rem;
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .stat-num { font-size: 1.9rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: .78rem; color: #64748b; margin-top: .25rem; }

        /* History card */
        .history-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 1rem; overflow: hidden;
        }
        .history-card .card-header {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 1.1rem 1.4rem;
        }
        .table > :not(caption) > * > * { padding: .85rem 1.1rem; }
        .table thead th {
            font-size: .72rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            color: #94a3b8; border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .table tbody tr { vertical-align: middle; }
        .table tbody tr:last-child td { border-bottom: 0; }

        /* Mini sentiment bar */
        .mini-bar-wrap { min-width: 120px; }
        .mini-bar {
            height: 6px; border-radius: 999px;
            background: #f1f5f9; overflow: hidden; display: flex;
        }
        .mini-bar-pos { background: #22c55e; }
        .mini-bar-neg { background: #ef4444; }

        /* Badges */
        .badge-pos { background: #dcfce7; color: #15803d; }
        .badge-neg { background: #fee2e2; color: #b91c1c; }

        /* Top reason preview */
        .reason-preview {
            font-size: .75rem; color: #64748b;
            max-width: 200px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }

        /* Empty state */
        .empty-state { padding: 4rem 1rem; text-align: center; color: #94a3b8; }
        .empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }
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

        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted d-none d-sm-inline">
                <i class="bi bi-person-circle me-1"></i>
                <strong class="text-dark">{{ Auth::user()->name }}</strong>
                @if (Auth::user()->is_guest)
                    <span class="badge bg-warning text-dark ms-1">Guest</span>
                @endif
            </span>
            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-right me-1"></i>Sign out
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ── Main ── --}}
<main class="container-lg py-5">

    {{-- Guest banner --}}
    @if (Auth::user()->is_guest)
        <div class="alert alert-warning d-flex align-items-center gap-2 small mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            You're browsing as a guest. Analyses created here are shared across all guest sessions.
        </div>
    @endif

    {{-- Flash success --}}
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 small mb-4" role="alert">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Page header ── --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Dashboard</h1>
            <p class="text-muted small mb-0">Welcome back, {{ Auth::user()->name }}.</p>
        </div>
        <a href="{{ route('analysis.create') }}" class="btn btn-primary fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> New Analysis
        </a>
    </div>

    {{-- ── Summary stats ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#eef2ff;color:#4f46e5;">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <div>
                    <div class="stat-num">{{ $totalAnalyses }}</div>
                    <div class="stat-label">Total Analyses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;">
                    <i class="bi bi-chat-square-text-fill"></i>
                </div>
                <div>
                    <div class="stat-num">{{ number_format($totalReviews) }}</div>
                    <div class="stat-label">Reviews Analyzed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdf4;color:#15803d;">
                    <i class="bi bi-emoji-smile-fill"></i>
                </div>
                <div>
                    <div class="stat-num">{{ $avgPositiveRate }}<span style="font-size:1rem;">%</span></div>
                    <div class="stat-label">Avg Positive Rate</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff7ed;color:#c2410c;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="font-size:1rem;line-height:1.3;">
                        @if ($analyses->isNotEmpty())
                            {{ $analyses->first()->created_at->format('d M') }}
                        @else
                            —
                        @endif
                    </div>
                    <div class="stat-label">Last Analysis</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── History ── --}}
    <div class="history-card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2 text-muted"></i>Analysis History
            </h6>
            <span class="badge rounded-pill bg-light text-secondary border">
                {{ $totalAnalyses }} {{ Str::plural('record', $totalAnalyses) }}
            </span>
        </div>

        @if ($analyses->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p class="fw-semibold mb-1 text-dark">No analyses yet</p>
                <p class="small mb-3">Upload a CSV to run your first product review analysis.</p>
                <a href="{{ route('analysis.create') }}" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-plus-lg me-1"></i> New Analysis
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Reviews</th>
                            <th style="min-width:130px;">Sentiment</th>
                            <th class="text-center d-none d-md-table-cell">
                                <i class="bi bi-emoji-smile-fill text-success me-1"></i>Positive
                            </th>
                            <th class="text-center d-none d-md-table-cell">
                                <i class="bi bi-emoji-frown-fill text-danger me-1"></i>Negative
                            </th>
                            <th class="d-none d-lg-table-cell">Top Negative Reason</th>
                            <th class="d-none d-sm-table-cell">Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analyses as $analysis)
                        <tr>
                            <td>
                                <a href="{{ route('analysis.show', $analysis) }}"
                                   class="fw-semibold text-dark text-decoration-none">
                                    {{ $analysis->product_name }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border">{{ $analysis->total_reviews }}</span>
                            </td>
                            <td>
                                <div class="mini-bar-wrap">
                                    <div class="mini-bar">
                                        <div class="mini-bar-pos" style="width:{{ $analysis->positivePercent() }}%"></div>
                                        <div class="mini-bar-neg" style="width:{{ $analysis->negativePercent() }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1" style="font-size:.68rem;color:#94a3b8;">
                                        <span style="color:#16a34a;">{{ $analysis->positivePercent() }}%</span>
                                        <span style="color:#dc2626;">{{ $analysis->negativePercent() }}%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge rounded-pill badge-pos">{{ $analysis->positive_count }}</span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge rounded-pill badge-neg">{{ $analysis->negative_count }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @php $topReason = $analysis->top_negative_reasons[0]['reason'] ?? null; @endphp
                                @if ($topReason)
                                    <span class="reason-preview" title="{{ $topReason }}">
                                        <i class="bi bi-exclamation-circle text-danger me-1"></i>{{ $topReason }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell text-muted small">
                                {{ $analysis->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('analysis.show', $analysis) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('analysis.destroy', $analysis) }}"
                                          onsubmit="return confirm('Delete this analysis?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
