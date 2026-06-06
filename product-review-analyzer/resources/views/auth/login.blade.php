<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — RenalSight</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('partials.favicon')
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 1rem;
        }
        .icon-box {
            width: 56px;
            height: 56px;
            background-color: #4f46e5;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.2);
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #9ca3af;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>

    <div class="container" style="max-width: 420px;">

        <div class="card shadow-lg p-4 p-md-5">

            {{-- Header --}}
            <div class="text-center mb-4">
                <div class="icon-box">
                    <img src="{{ asset('renalsight-favicons/favicon-48x48.png') }}" alt="RenalSight" width="38" height="38" style="border-radius:.5rem;">
                </div>
                <h1 class="h4 fw-bold text-dark mb-1">RenalSight</h1>
                <p class="text-muted small">Sign in to your account</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 small" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-medium small">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="form-control @error('email') is-invalid @enderror"
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium small">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="form-control"
                    >
                </div>

                <div class="mb-4 form-check">
                    <input id="remember" type="checkbox" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small text-muted">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Sign in
                </button>
            </form>

            {{-- Divider --}}
            <div class="divider my-4">or</div>

            {{-- Guest Login --}}
            <form method="POST" action="{{ route('guest.login') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Continue as Guest
                </button>
            </form>

            {{-- Back to landing --}}
            <div class="text-center mt-4">
                <a href="{{ route('landing') }}" class="text-muted small text-decoration-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="me-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to home
                </a>
            </div>

        </div>

        <p class="text-center text-white-50 small mt-4">
            &copy; {{ date('Y') }} RenalSight
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
