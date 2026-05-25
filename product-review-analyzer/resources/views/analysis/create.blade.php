<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Analysis — Product Review Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .navbar-brand-icon {
            width: 34px; height: 34px; background: #4f46e5; border-radius: .55rem;
            display: flex; align-items: center; justify-content: center;
        }
        .form-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 1rem; padding: 2rem;
        }
        .form-label { font-weight: 600; font-size: .875rem; }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 .2rem rgba(79,70,229,.15);
        }

        /* ── Drop zone ── */
        .drop-zone {
            border: 2px dashed #c7d2fe; border-radius: .75rem;
            background: #f8faff; padding: 2.5rem 1.5rem;
            text-align: center; cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
        }
        .drop-zone:hover, .drop-zone.dragover { border-color: #4f46e5; background: #eef2ff; }
        .drop-zone.has-file { border-color: #4f46e5; background: #eef2ff; border-style: solid; }
        .drop-zone input[type="file"] {
            position: absolute; inset: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }
        .drop-icon { font-size: 2.4rem; color: #a5b4fc; }

        /* ── Preview table ── */
        #previewSection { display: none; }
        .preview-table-wrap {
            border: 1px solid #e2e8f0; border-radius: .6rem; overflow: hidden;
        }
        .preview-table { font-size: .76rem; margin-bottom: 0; }
        .preview-table thead th {
            background: #f8fafc; font-weight: 700;
            text-transform: uppercase; font-size: .68rem;
            letter-spacing: .05em; color: #64748b;
            padding: .6rem .8rem; border-bottom: 1px solid #e2e8f0;
        }
        .preview-table tbody td { padding: .55rem .8rem; color: #374151; }
        .preview-table tbody tr:last-child td { border-bottom: 0; }

        .stat-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;
            border-radius: 999px; padding: .2rem .75rem; font-size: .78rem; font-weight: 600;
        }

        .btn-primary { background: #4f46e5; border-color: #4f46e5; }
        .btn-primary:hover { background: #4338ca; border-color: #4338ca; }
        .btn-primary:disabled { background: #818cf8; border-color: #818cf8; }

        .info-box {
            background: #eef2ff; border: 1px solid #c7d2fe;
            border-radius: .6rem; padding: .9rem 1rem;
            font-size: .82rem; color: #4338ca;
        }

        /* ── Loading overlay ── */
        #loadingOverlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
        }
        #loadingOverlay.d-none { display: none !important; }
        .overlay-card {
            background: #fff; border-radius: 1.25rem;
            padding: 2.5rem 3rem; text-align: center;
            box-shadow: 0 24px 60px rgba(0,0,0,.22);
            max-width: 340px; width: 90%;
        }
        .overlay-card .spinner-border {
            width: 3rem; height: 3rem;
            color: #4f46e5; border-width: .25em;
        }
        .typing-dots { letter-spacing: .1em; }
        .typing-dots span {
            display: inline-block;
            animation: dotPulse 1.4s infinite ease-in-out;
            font-size: 1.4rem; color: #4f46e5; line-height: 1;
        }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes dotPulse {
            0%, 80%, 100% { opacity: 0.1; transform: translateY(0); }
            40%            { opacity: 1;   transform: translateY(-4px); }
        }

        /* ── Inline API error ── */
        #apiErrorBox { display: none; align-items: flex-start; }
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
        <span class="small text-muted d-none d-sm-inline">
            <i class="bi bi-person-circle me-1"></i>
            <strong class="text-dark">{{ Auth::user()->name }}</strong>
            @if (Auth::user()->is_guest)
                <span class="badge bg-warning text-dark ms-1">Guest</span>
            @endif
        </span>
    </div>
</nav>

{{-- ── Main ── --}}
<main class="container-lg py-5" style="max-width: 760px;">

    <a href="{{ route('home') }}" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 mb-4">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">New Analysis</h1>
        <p class="text-muted small mb-0">
            Select a product and upload a CSV of reviews. The API will classify each review and surface the
            <strong>top 3 negative sentiment reasons</strong>.
        </p>
    </div>

    {{-- API error --}}
    @if ($errors->has('api'))
        <div class="alert alert-danger d-flex gap-2 align-items-start small mb-4" role="alert">
            <i class="bi bi-x-circle-fill flex-shrink-0 mt-1"></i>
            <div><strong>Analysis failed.</strong><br>{{ $errors->first('api') }}</div>
        </div>
    @endif

    <div class="info-box mb-4 d-flex gap-2">
        <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
        <span>
            Your CSV must have a header row with a <strong><code>review_text</code></strong> column.
            Only that column is extracted and sent to the API.
            <strong>Max size: 20 MB.</strong>
        </span>
    </div>

    {{-- Inline API error (shown after async failure) --}}
    <div id="apiErrorBox" class="alert alert-danger gap-2 align-items-start small mb-4" role="alert" style="display:none">
        <i class="bi bi-x-circle-fill flex-shrink-0 mt-1"></i>
        <div><strong>Analysis failed.</strong><br><span id="apiErrorMsg"></span></div>
    </div>

    {{-- ── Form ── --}}
    <div class="form-card shadow-sm">

        @if ($products->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-box text-muted" style="font-size:2.5rem;display:block;margin-bottom:.75rem;"></i>
                <p class="fw-semibold text-dark mb-1">No products found</p>
                <p class="text-muted small mb-3">Add a product from the dashboard before running an analysis.</p>
                <a href="{{ route('home') }}" class="btn btn-primary px-4">
                    <i class="bi bi-box-seam me-1"></i> Go Add a Product
                </a>
            </div>
        @else

        <form method="POST" action="{{ route('analysis.store') }}" enctype="multipart/form-data" id="analysisForm">
            @csrf

            {{-- Product selection --}}
            <div class="mb-4">
                <label for="product_id" class="form-label">
                    Product <span class="text-danger">*</span>
                </label>
                <select id="product_id" name="product_id" required
                        class="form-select @error('product_id') is-invalid @enderror">
                    <option value="">— Select a product —</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                            {{ $product->name }}{{ $product->category ? '  ·  ' . $product->category->name : '' }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="mt-1">
                    <a href="{{ route('home') }}" class="small text-primary text-decoration-none">
                        <i class="bi bi-plus-lg me-1"></i>Add new product
                    </a>
                </div>
            </div>

            {{-- CSV upload --}}
            <div class="mb-4">
                <label class="form-label d-block mb-2">
                    Reviews CSV <span class="text-danger">*</span>
                </label>

                <div class="drop-zone @error('csv_file') border-danger @enderror" id="dropZone">
                    <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt" required>
                    <div id="dropContent">
                        <div class="drop-icon mb-2"><i class="bi bi-file-earmark-arrow-up"></i></div>
                        <div class="fw-semibold" style="color:#4338ca;">Click to upload or drag &amp; drop</div>
                        <div class="text-muted small mt-1">CSV or TXT &nbsp;·&nbsp; max 20 MB</div>
                    </div>
                </div>

                @error('csv_file')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── PapaParse preview ── --}}
            <div id="previewSection" class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <span class="fw-semibold small text-dark">
                        <i class="bi bi-table me-1 text-muted"></i>CSV Preview
                    </span>
                    <div class="d-flex gap-2 flex-wrap" id="csvStats"></div>
                </div>

                <div id="parseError" class="alert alert-danger small py-2 d-none"></div>

                <div class="preview-table-wrap">
                    <div class="table-responsive" style="max-height: 240px; overflow-y: auto;">
                        <table class="table table-hover preview-table">
                            <thead id="previewHead"></thead>
                            <tbody id="previewBody"></tbody>
                        </table>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:.75rem;">
                    Showing first 5 rows. All rows will be sent to the API.
                </p>
            </div>

            {{-- Actions --}}
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary fw-semibold px-4" id="submitBtn" disabled>
                    <i class="bi bi-lightning-charge-fill me-1"></i> Run Analysis
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="button" class="btn btn-link text-muted small p-0 ms-auto" id="clearBtn" style="display:none;">
                    <i class="bi bi-x-circle me-1"></i>Clear file
                </button>
            </div>

        </form>

        @endif
    </div>

</main>

{{-- ── Loading overlay ── --}}
<div id="loadingOverlay" class="d-none">
    <div class="overlay-card">
        <div class="spinner-border mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h6 class="fw-bold text-dark mb-1">Analyzing Reviews</h6>
        <p class="text-muted small mb-2">Sending data to the API. This may take a moment…</p>
        <div class="typing-dots mb-4">
            <span>●</span><span>●</span><span>●</span>
        </div>
        <button id="cancelAnalysisBtn" type="button"
                class="btn btn-outline-secondary btn-sm px-4">
            <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script>
    const dropZone     = document.getElementById('dropZone');
    const fileInput    = document.getElementById('csvFile');
    const dropContent  = document.getElementById('dropContent');
    const previewSec   = document.getElementById('previewSection');
    const previewHead  = document.getElementById('previewHead');
    const previewBody  = document.getElementById('previewBody');
    const csvStats     = document.getElementById('csvStats');
    const parseErrorEl = document.getElementById('parseError');
    const submitBtn    = document.getElementById('submitBtn');
    const clearBtn     = document.getElementById('clearBtn');
    const overlay           = document.getElementById('loadingOverlay');
    const apiErrorBox       = document.getElementById('apiErrorBox');
    const apiErrorMsg       = document.getElementById('apiErrorMsg');
    const cancelAnalysisBtn = document.getElementById('cancelAnalysisBtn');
    let   abortController   = null;

    // ── Drag-and-drop ──
    if (dropZone) {
        dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFile(fileInput.files[0]);
            }
        });
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) handleFile(fileInput.files[0]);
        });
        clearBtn.addEventListener('click', resetFile);
    }

    function handleFile(file) {
        dropZone.classList.add('has-file');
        dropContent.innerHTML = `
            <div class="drop-icon mb-1" style="font-size:2rem;">
                <i class="bi bi-file-earmark-check" style="color:#4f46e5;"></i>
            </div>
            <div class="fw-semibold" style="color:#4f46e5;">${escHtml(file.name)}</div>
            <div class="text-muted small mt-1">${(file.size / 1024).toFixed(1)} KB</div>
        `;
        clearBtn.style.display = 'inline-block';

        Papa.parse(file, {
            header: true,
            skipEmptyLines: true,
            preview: 0,
            complete: results => renderPreview(results),
            error:   err     => showParseError(err.message),
        });
    }

    function renderPreview(results) {
        parseErrorEl.classList.add('d-none');

        const fields    = results.meta.fields ?? [];
        const allRows   = results.data;
        const totalRows = allRows.length;
        const preview   = allRows.slice(0, 5);

        if (fields.length === 0 || totalRows === 0) {
            showParseError('The CSV appears to be empty or has no valid header row.');
            return;
        }

        // Warn if review_text column is missing
        const hasReviewText = fields.map(f => f.toLowerCase().trim()).includes('review_text');
        if (!hasReviewText) {
            showParseError('No "review_text" column found. Please ensure your CSV has a "review_text" header.');
            return;
        }

        csvStats.innerHTML = `
            <span class="stat-pill"><i class="bi bi-list-ul"></i> ${totalRows.toLocaleString()} rows</span>
            <span class="stat-pill"><i class="bi bi-layout-three-columns"></i> ${fields.length} columns</span>
        `;

        previewHead.innerHTML = '<tr>' + fields.map(f => {
            const isReview = f.toLowerCase().trim() === 'review_text';
            return `<th ${isReview ? 'style="color:#4f46e5;"' : ''}>${escHtml(f)}</th>`;
        }).join('') + '</tr>';

        previewBody.innerHTML = preview.map(row =>
            '<tr>' + fields.map(f => `<td>${escHtml(String(row[f] ?? ''))}</td>`).join('') + '</tr>'
        ).join('');

        previewSec.style.display = 'block';
        submitBtn.disabled = false;
    }

    function showParseError(msg) {
        parseErrorEl.textContent = '⚠ ' + msg;
        parseErrorEl.classList.remove('d-none');
        previewSec.style.display = 'block';
        previewHead.innerHTML = '';
        previewBody.innerHTML = '';
        csvStats.innerHTML    = '';
        submitBtn.disabled    = true;
    }

    function resetFile() {
        fileInput.value = '';
        dropZone.classList.remove('has-file');
        dropContent.innerHTML = `
            <div class="drop-icon mb-2"><i class="bi bi-file-earmark-arrow-up"></i></div>
            <div class="fw-semibold" style="color:#4338ca;">Click to upload or drag &amp; drop</div>
            <div class="text-muted small mt-1">CSV or TXT &nbsp;·&nbsp; max 20 MB</div>
        `;
        previewSec.style.display = 'none';
        csvStats.innerHTML    = '';
        previewHead.innerHTML = '';
        previewBody.innerHTML = '';
        parseErrorEl.classList.add('d-none');
        submitBtn.disabled    = true;
        clearBtn.style.display = 'none';
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Cancel button ──
    cancelAnalysisBtn?.addEventListener('click', () => {
        if (abortController) {
            abortController.abort();
            abortController = null;
        }
    });

    // ── Async form submission ──
    document.getElementById('analysisForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Hide any previous error
        hideApiError();

        // Create a fresh abort controller for this request
        abortController = new AbortController();

        // Auto-cancel after 5 minutes (matches ngrok + Laravel timeout)
        const TIMEOUT_MS = 5 * 60 * 1000;
        const autoTimeout = setTimeout(() => abortController.abort('timeout'), TIMEOUT_MS);

        overlay.classList.remove('d-none');
        submitBtn.disabled = true;

        try {
            const response = await fetch(this.action, {
                method:  'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
                body:   new FormData(this),
                signal: abortController.signal,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.href = data.redirect;
            } else {
                hideOverlay();
                if (data.errors) {
                    showApiError(Object.values(data.errors).flat()[0]);
                } else {
                    showApiError(data.error ?? 'An unexpected error occurred.');
                }
            }
        } catch (err) {
            hideOverlay();
            if (err.name === 'AbortError') {
                const isTimeout = abortController?.signal?.reason === 'timeout';
                if (isTimeout) {
                    showApiError('Request timed out after 5 minutes. The server may still be processing — check your dashboard shortly.');
                } else {
                    showApiError('Analysis cancelled. You can try again whenever you\'re ready.', 'warning');
                }
            } else {
                showApiError('Network error — could not reach the server. Please try again.');
            }
        } finally {
            clearTimeout(autoTimeout);
            abortController = null;
        }
    });

    function hideOverlay() {
        overlay.classList.add('d-none');
        submitBtn.disabled = false;
    }

    function showApiError(msg, type = 'danger') {
        apiErrorBox.className = `alert alert-${type} gap-2 align-items-start small mb-4`;
        apiErrorBox.style.display = 'flex';
        apiErrorMsg.textContent = msg;
        apiErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideApiError() {
        apiErrorBox.style.display = 'none';
    }
</script>
</body>
</html>
