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

        /* Section card */
        .section-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 1rem; overflow: hidden;
        }
        .section-card .card-header {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.4rem;
        }

        /* Product cards */
        .product-card {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: .75rem; padding: .85rem 1rem; height: 100%;
            transition: border-color .15s, background .15s;
        }
        .product-card:hover { border-color: #c7d2fe; background: #eef2ff; }
        .category-badge {
            background: #ede9fe; color: #6d28d9;
            font-size: .68rem; font-weight: 600; border-radius: 999px;
            padding: .15rem .55rem;
        }

        /* History table */
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
        .empty-state { padding: 3rem 1rem; text-align: center; color: #94a3b8; }
        .empty-state > i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

        /* Floating action button */
        .fab-btn {
            width: 56px !important; height: 56px !important;
            padding: 0 !important;
            display: inline-flex !important; align-items: center; justify-content: center;
            font-size: 1.5rem; line-height: 1;
            box-shadow: 0 4px 14px rgba(79,70,229,.45);
            transition: transform .15s, box-shadow .15s;
        }
        .fab-btn i { display: inline; margin: 0; font-size: inherit; vertical-align: middle; }
        .fab-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(79,70,229,.55);
        }

        /* Active filter pill */
        .filter-pill {
            background: #eef2ff; border: 1px solid #c7d2fe;
            border-radius: 999px; padding: .2rem .7rem;
            font-size: .75rem; color: #4338ca; font-weight: 600;
        }

        /* Category modal list items */
        .cat-list-item {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: .5rem; padding: .6rem .85rem;
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
            You're browsing as a guest. Analyses and products created here are shared across all guest sessions.
        </div>
    @endif

    {{-- Flash --}}
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
                        @php $latest = \App\Models\Analysis::where('user_id', Auth::id())->latest()->first(); @endphp
                        @if ($latest)
                            {{ $latest->created_at->format('d M') }}
                        @else
                            —
                        @endif
                    </div>
                    <div class="stat-label">Last Analysis</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── My Products ── --}}
    <div class="section-card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-box-seam me-2 text-muted"></i>My Products
                <span class="badge rounded-pill bg-light text-secondary border ms-1">{{ $products->count() }}</span>
            </h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-tags me-1"></i> Categories
                </button>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="bi bi-plus-lg me-1"></i> New Product
                </button>
            </div>
        </div>

        <div class="p-3">
            @if ($products->isEmpty())
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-box" style="font-size:1.8rem;display:block;margin-bottom:.5rem;"></i>
                    <p class="small mb-2">No products yet. Add a product to start analyzing reviews.</p>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Product
                    </button>
                </div>
            @else
                <div class="row g-2">
                    @foreach ($products as $product)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="product-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-2" style="min-width:0;">
                                    <div class="fw-semibold small text-truncate">{{ $product->name }}</div>
                                    @if ($product->category)
                                        <span class="badge category-badge mt-1">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted" style="font-size:.7rem;">No category</span>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('Delete \'{{ addslashes($product->name) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger p-1" style="line-height:1;" title="Delete">
                                        <i class="bi bi-trash" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="mt-2 text-muted" style="font-size:.72rem;">
                                <i class="bi bi-bar-chart-fill me-1"></i>
                                {{ $product->analyses_count }} {{ Str::plural('analysis', $product->analyses_count) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Analysis History ── --}}
    <div class="section-card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2 text-muted"></i>Analysis History
                <span class="badge rounded-pill bg-light text-secondary border ms-1">
                    {{ $totalAnalyses }} {{ Str::plural('record', $totalAnalyses) }}
                </span>
            </h6>

            {{-- Search + category filter --}}
            <form method="GET" action="{{ route('home') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width:210px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted" style="font-size:.8rem;"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Search product…"
                           class="form-control border-start-0 ps-0">
                </div>

                @if ($categories->isNotEmpty())
                    <select name="category" class="form-select form-select-sm" style="width:160px;"
                            onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel"></i>
                </button>

                @if (request('q') || request('category'))
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-danger" title="Clear filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Active filter indicator --}}
        @if (request('q') || request('category'))
            <div class="px-4 py-2 border-bottom d-flex align-items-center gap-2 flex-wrap"
                 style="background:#fafbff; font-size:.78rem;">
                <span class="text-muted">Filtering by:</span>
                @if (request('q'))
                    <span class="filter-pill"><i class="bi bi-search me-1"></i>{{ request('q') }}</span>
                @endif
                @if (request('category'))
                    @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                    @if ($activeCat)
                        <span class="filter-pill"><i class="bi bi-tag me-1"></i>{{ $activeCat->name }}</span>
                    @endif
                @endif
                <span class="text-muted">·
                    {{ $analyses->count() }} {{ Str::plural('result', $analyses->count()) }}
                </span>
            </div>
        @endif

        @if ($analyses->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                @if (request('q') || request('category'))
                    <p class="fw-semibold mb-1 text-dark">No matching analyses</p>
                    <p class="small mb-3">Try adjusting your search or clearing the filter.</p>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm px-4">
                        <i class="bi bi-x me-1"></i> Clear Filters
                    </a>
                @else
                    <p class="fw-semibold mb-1 text-dark">No analyses yet</p>
                    <p class="small mb-3">Upload a CSV to run your first product review analysis.</p>
                    <a href="{{ route('analysis.create') }}"
                       class="btn btn-primary rounded-circle fab-btn" title="New Analysis">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="d-none d-md-table-cell">Category</th>
                            <th class="text-center">Reviews</th>
                            <th style="min-width:130px;">Sentiment</th>
                            <th class="text-center d-none d-md-table-cell">
                                <i class="bi bi-emoji-smile-fill text-success me-1"></i>Pos
                            </th>
                            <th class="text-center d-none d-md-table-cell">
                                <i class="bi bi-emoji-frown-fill text-danger me-1"></i>Neg
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
                            <td class="d-none d-md-table-cell">
                                @if ($analysis->product?->category)
                                    <span class="badge category-badge">{{ $analysis->product->category->name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
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
                                @php
                                    $topReason = $analysis->product_reasons[0]['reason']
                                        ?? $analysis->shipping_reasons[0]['reason']
                                        ?? null;
                                    $isShipping = empty($analysis->product_reasons) && !empty($analysis->shipping_reasons);
                                @endphp
                                @if ($topReason)
                                    <span class="reason-preview" title="{{ $topReason }}">
                                        <i class="bi bi-{{ $isShipping ? 'truck' : 'exclamation-circle' }} text-danger me-1"></i>{{ $topReason }}
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

{{-- ── Add Product Modal ── --}}
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                <input type="hidden" name="_form" value="product">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="productModalLabel">
                        <i class="bi bi-box-seam me-2" style="color:#4f46e5;"></i>New Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Product Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" required class="form-control"
                               placeholder="e.g. Wireless Headphones X3"
                               value="{{ old('_form') === 'product' ? old('name') : '' }}">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold small">Category <span class="text-muted fw-normal">(optional)</span></label>
                        <select name="category_id" class="form-select">
                            <option value="">— No category —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    @selected(old('_form') === 'product' && old('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-1">
                            <button type="button" class="btn btn-link btn-sm p-0 text-primary"
                                    data-bs-dismiss="modal"
                                    data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="bi bi-plus-lg me-1"></i>Add new category
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Manage Categories Modal ── --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="categoryModalLabel">
                    <i class="bi bi-tags me-2" style="color:#4f46e5;"></i>Manage Categories
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('product-categories.store') }}" class="mb-4">
                    @csrf
                    <label class="form-label fw-semibold small">
                        New Category Name <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" name="name" required class="form-control"
                               placeholder="e.g. Electronics">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Add
                        </button>
                    </div>
                </form>

                @if ($categories->isNotEmpty())
                    <div class="border-top pt-3">
                        <p class="small fw-semibold text-muted mb-2 text-uppercase" style="letter-spacing:.05em;">
                            Existing Categories
                        </p>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($categories as $cat)
                            <div class="cat-list-item d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-semibold small">{{ $cat->name }}</span>
                                    <span class="text-muted ms-2" style="font-size:.72rem;">
                                        {{ $cat->products->count() }} {{ Str::plural('product', $cat->products->count()) }}
                                    </span>
                                </div>
                                <form method="POST"
                                      action="{{ route('product-categories.destroy', $cat) }}"
                                      onsubmit="return confirm('Delete \'{{ addslashes($cat->name) }}\'? Products will become uncategorized.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger p-1" style="line-height:1;">
                                        <i class="bi bi-trash" style="font-size:.75rem;"></i>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-muted small mt-1 mb-0">No categories yet.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-reopen modal after redirect if that form had errors
    @if (old('_form') === 'product')
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('productModal')).show();
        });
    @endif
</script>
</body>
</html>
