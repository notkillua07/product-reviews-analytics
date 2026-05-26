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

        /* Product reasons — red theme */
        .reason-card.product-reason { border: 1px solid #fee2e2; background: #fff5f5; }
        .product-reason .reason-rank { background: #ef4444; }
        .product-reason .reason-rank.r2 { background: #f97316; }
        .product-reason .reason-rank.r3 { background: #f59e0b; }
        .product-reason .reason-count { color: #ef4444; }

        /* Shipping reasons — amber theme */
        .reason-card.shipping-reason { border: 1px solid #fde68a; background: #fffbeb; }
        .shipping-reason .reason-rank { background: #f59e0b; }
        .shipping-reason .reason-rank.r2 { background: #fbbf24; }
        .shipping-reason .reason-rank.r3 { background: #fcd34d; color: #78350f; }
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
            <div class="navbar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                </svg>
            </div>
            <span class="fw-bold text-dark small">ReviewAnalyzer</span>
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

    {{-- ── Negative Reason Categories ── --}}
    <div class="row g-3 mb-4">

        {{-- Product Issues --}}
        <div class="col-lg-6">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-box-seam-fill" style="color:#ef4444;"></i>
                    Product Issues
                </p>

                @if (!empty($analysis->product_reasons))
                    @foreach ($analysis->product_reasons as $i => $reason)
                        <div class="reason-card product-reason">
                            <div class="reason-rank {{ $i === 1 ? 'r2' : ($i === 2 ? 'r3' : '') }}">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <div class="reason-text">{{ $reason['reason'] }}</div>
                                @if (!empty($reason['count']))
                                    <div class="reason-count">
                                        <i class="bi bi-chat-left-text-fill me-1"></i>
                                        {{ $reason['count'] }} {{ Str::plural('mention', $reason['count']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="reason-empty mb-0">No product-related issues found.</p>
                @endif
            </div>
        </div>

        {{-- Shipping Issues --}}
        <div class="col-lg-6">
            <div class="section-card h-100">
                <p class="section-title">
                    <i class="bi bi-truck" style="color:#d97706;"></i>
                    Shipping Issues
                </p>

                @if (!empty($analysis->shipping_reasons))
                    @foreach ($analysis->shipping_reasons as $i => $reason)
                        <div class="reason-card shipping-reason">
                            <div class="reason-rank {{ $i === 1 ? 'r2' : ($i === 2 ? 'r3' : '') }}">
                                {{ $i + 1 }}
                            </div>
                            <div>
                                <div class="reason-text">{{ $reason['reason'] }}</div>
                                @if (!empty($reason['count']))
                                    <div class="reason-count">
                                        <i class="bi bi-chat-left-text-fill me-1"></i>
                                        {{ $reason['count'] }} {{ Str::plural('mention', $reason['count']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="reason-empty mb-0">No shipping-related issues found.</p>
                @endif
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

    </div>{{-- /pdf-summary --}}

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
                            <th style="width:50px;">#</th>
                            <th>Review</th>
                            <th style="width:120px;" class="text-center">Sentiment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analysis->reviews_data as $review)
                        <tr data-sentiment="{{ $review['label'] ?? 'unknown' }}">
                            <td class="text-muted small">{{ $review['id'] ?? ($loop->index) }}</td>
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

        {{-- Pagination bar --}}
        @if (!empty($analysis->reviews_data))
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    const ROWS_PER_PAGE = 15;
    let currentPage = 1;
    let currentFilter = 'all';

    function allRows() {
        return Array.from(document.querySelectorAll('#reviewsTable tbody tr'));
    }

    function filteredRows() {
        return allRows().filter(row =>
            currentFilter === 'all' || row.dataset.sentiment === currentFilter
        );
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

    // PDF export — summary as image, reviews as real text
    async function exportPDF() {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating…';

        try {
            const { jsPDF } = window.jspdf;
            const doc     = new jsPDF({ unit: 'mm', format: 'a4', orientation: 'portrait' });
            const margin  = 10;
            const pageW   = doc.internal.pageSize.getWidth();
            const pageH   = doc.internal.pageSize.getHeight();
            const usableW = pageW - margin * 2;

            // ── 1. Capture summary section as high-quality image ──
            const summaryEl = document.getElementById('pdf-summary');
            const canvas    = await html2canvas(summaryEl, {
                scale: 2, useCORS: true, logging: false, backgroundColor: '#f1f5f9'
            });
            const imgData = canvas.toDataURL('image/jpeg', 0.92);
            const imgH    = (canvas.height / canvas.width) * usableW;

            // If summary fits on one page, add it; otherwise split across pages
            const availH = pageH - margin * 2;
            if (imgH <= availH) {
                doc.addImage(imgData, 'JPEG', margin, margin, usableW, imgH);
            } else {
                // Slice image across pages using a temporary canvas
                const sliceCanvas  = document.createElement('canvas');
                const pxPerMm      = canvas.width / usableW;
                const slicePxH     = Math.floor(availH * pxPerMm);
                sliceCanvas.width  = canvas.width;
                const ctx          = sliceCanvas.getContext('2d');
                let srcY = 0;
                let first = true;
                while (srcY < canvas.height) {
                    const thisPxH = Math.min(slicePxH, canvas.height - srcY);
                    sliceCanvas.height = thisPxH;
                    ctx.clearRect(0, 0, sliceCanvas.width, thisPxH);
                    ctx.drawImage(canvas, 0, srcY, canvas.width, thisPxH, 0, 0, canvas.width, thisPxH);
                    const sliceData = sliceCanvas.toDataURL('image/jpeg', 0.92);
                    const sliceMmH  = thisPxH / pxPerMm;
                    if (!first) doc.addPage();
                    doc.addImage(sliceData, 'JPEG', margin, margin, usableW, sliceMmH);
                    srcY += thisPxH;
                    first = false;
                }
            }

            // ── 2. Add reviews table as real text on a new page ──
            doc.addPage();

            doc.setFontSize(11);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(30, 41, 59);
            doc.text('ALL REVIEWS', margin, margin + 4);

            const rows  = filteredRows();
            const label = currentFilter === 'all'
                ? 'All reviews'
                : currentFilter.charAt(0).toUpperCase() + currentFilter.slice(1) + ' reviews';
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(100, 116, 139);
            doc.text(`${label}: ${rows.length}`, margin, margin + 9);

            const tableData = rows.map(row => [
                row.cells[0].textContent.trim(),
                row.cells[1].textContent.trim(),
                row.dataset.sentiment === 'positive' ? 'Positive' : 'Negative',
            ]);

            doc.autoTable({
                startY: margin + 13,
                head:   [['#', 'Review', 'Sentiment']],
                body:   tableData,
                margin: { left: margin, right: margin },
                styles:      { fontSize: 8, cellPadding: 2.5, overflow: 'linebreak' },
                headStyles:  { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
                columnStyles: {
                    0: { cellWidth: 10, halign: 'center' },
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 24, halign: 'center' },
                },
                didParseCell(data) {
                    if (data.section === 'body' && data.column.index === 2) {
                        data.cell.styles.textColor = data.cell.raw === 'Positive'
                            ? [21, 128, 61]
                            : [185, 28, 28];
                        data.cell.styles.fontStyle = 'bold';
                    }
                },
            });

            doc.save('{{ Str::slug($analysis->product_name) }}_analysis.pdf');
        } finally {
            renderTable();
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Export PDF';
        }
    }

    // Init
    renderTable();
</script>
</body>
</html>
