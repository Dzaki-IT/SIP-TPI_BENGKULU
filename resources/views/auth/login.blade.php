<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - SIP-TPI Bengkulu</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons 1.11.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-navy: #0B3C5D;
            --dark-navy: #1D2731;
            --ocean-blue: #328CC1;
            --ocean-cyan: #47b8e0;
            --ocean-light: #eaf4fb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0B3C5D 0%, #102A43 45%, #1D2731 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle marine wave pattern decoration */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(rgba(50, 140, 193, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(11, 60, 93, 0.28);
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #0B3C5D 0%, #164e75 100%);
            color: #ffffff;
            padding: 2.25rem 2rem 1.75rem;
            text-align: center;
            position: relative;
        }

        .header-icon-circle {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            color: #63b3ed;
            margin-bottom: 0.85rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .system-acronym {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 0.35rem;
        }

        .system-title {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            line-height: 1.4;
            color: #cbd5e1;
            margin-bottom: 0.25rem;
        }

        .agency-name {
            font-size: 0.72rem;
            color: #90cdf4;
            font-weight: 500;
            letter-spacing: 0.4px;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--ocean-blue);
        }

        .form-control:focus {
            border-color: var(--ocean-blue);
            box-shadow: 0 0 0 0.25rem rgba(50, 140, 193, 0.2);
        }

        .btn-ocean {
            background-color: var(--primary-navy);
            color: #ffffff;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            border: none;
        }

        .btn-ocean:hover {
            background-color: #164e75;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(11, 60, 93, 0.35);
        }

        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1.15rem;
            margin-top: 1.5rem;
        }

        .demo-btn {
            font-size: 0.78rem;
            padding: 0.45rem 0.65rem;
            border-radius: 8px;
            text-align: left;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .demo-btn:hover {
            border-color: var(--ocean-blue);
            background: var(--ocean-light);
            transform: translateY(-1px);
        }

        .demo-role-badge {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="header-icon-circle">
                <i class="bi bi-water"></i>
            </div>
            <div class="system-acronym">SIP-TPI PROVINSI BENGKULU</div>
            <div class="system-title">SISTEM INFORMASI TATA KELOLA DATA AKTIVITAS TPI</div>
            <div class="agency-name">Dinas Kelautan dan Perikanan Provinsi Bengkulu</div>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 small mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-dark small mb-1">
                        <i class="bi bi-envelope me-1 text-secondary"></i> Alamat Email
                    </label>
                    <input type="email" 
                           class="form-control form-control-lg fs-6 @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="nama@tpi-bengkulu.test" 
                           required 
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold text-dark small mb-0">
                            <i class="bi bi-shield-lock me-1 text-secondary"></i> Kata Sandi
                        </label>
                    </div>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control form-control-lg fs-6 @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" id="btnTogglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback small">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted small" for="remember">
                        Ingat sesi masuk saya
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-ocean btn-lg fs-6">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Sistem
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials for Testing & Thesis Examination -->
            <div class="demo-box">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-dark" style="font-size: 0.78rem;">
                        <i class="bi bi-key-fill text-warning me-1"></i> Demo Cepat Akun Uji Coba:
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Klik untuk mengisi</span>
                </div>

                <div class="d-flex flex-column gap-2">
                    <!-- Admin TPI Pulau Baai -->
                    <button type="button" class="btn demo-btn" onclick="fillDemo('admin@tpi-bengkulu.test', 'password')">
                        <div>
                            <div class="fw-semibold text-dark">Admin TPI Pulau Baai</div>
                            <div class="text-muted" style="font-size: 0.72rem;">admin@tpi-bengkulu.test</div>
                        </div>
                        <span class="demo-role-badge bg-primary text-white">Admin TPI</span>
                    </button>

                    <!-- Verifikator DKP -->
                    <button type="button" class="btn demo-btn" onclick="fillDemo('verifikator@tpi-bengkulu.test', 'password')">
                        <div>
                            <div class="fw-semibold text-dark">Verifikator DKP Provinsi</div>
                            <div class="text-muted" style="font-size: 0.72rem;">verifikator@tpi-bengkulu.test</div>
                        </div>
                        <span class="demo-role-badge bg-info text-white">Verifikator</span>
                    </button>

                    <!-- Pimpinan DKP -->
                    <button type="button" class="btn demo-btn" onclick="fillDemo('pimpinan@tpi-bengkulu.test', 'password')">
                        <div>
                            <div class="fw-semibold text-dark">Pimpinan DKP Provinsi</div>
                            <div class="text-muted" style="font-size: 0.72rem;">pimpinan@tpi-bengkulu.test</div>
                        </div>
                        <span class="demo-role-badge bg-purple text-white" style="background-color: #7c3aed;">Pimpinan</span>
                    </button>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center text-muted mt-3" style="font-size: 0.72rem;">
                &copy; {{ date('Y') }} Dinas Kelautan dan Perikanan Provinsi Bengkulu
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function fillDemo(email, password) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            emailInput.value = email;
            passwordInput.value = password;
            emailInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');

            // Visual feedback flash effect
            emailInput.focus();
        }

        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
