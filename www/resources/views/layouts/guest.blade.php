<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SGE') }} - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script>
        const storedTheme = localStorage.getItem('theme');
        const getPreferredTheme = () => {
            if (storedTheme) return storedTheme;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
        document.documentElement.setAttribute('data-bs-theme', getPreferredTheme() === 'auto' ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') : getPreferredTheme());
    </script>
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-card { border: 1px solid var(--bs-border-color); border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .brand-logo { font-weight: 900; letter-spacing: -1px; font-size: 3rem; }
    </style>
</head>
<body class="d-flex align-items-center py-4 min-vh-100 bg-body-tertiary">
    <main class="w-100 m-auto" style="max-width: 450px;">
        <div class="text-center mb-4">
            <h1 class="brand-logo text-primary">SGE<span class="text-body">.</span></h1>
            <p class="text-muted">Sistema de Gestão Escolar</p>
        </div>

        <div class="card login-card p-4">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
        
        <p class="mt-5 mb-3 text-muted text-center">&copy; {{ date('Y') }} SGE Solutions</p>
    </main>

    <!-- Theme Switcher -->
    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
        <button class="btn btn-secondary py-2 dropdown-toggle d-flex align-items-center shadow-sm"
                id="bd-theme"
                type="button"
                aria-expanded="false"
                data-bs-toggle="dropdown"
                aria-label="Toggle theme (auto)">
            <i class="bi bi-circle-half my-1 theme-icon-active"></i>
            <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                    <i class="bi bi-sun-fill me-2 opacity-50"></i> Claro
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                    <i class="bi bi-moon-stars-fill me-2 opacity-50"></i> Escuro
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
                    <i class="bi bi-circle-half me-2 opacity-50"></i> Auto (Sistema)
                </button>
            </li>
        </ul>
    </div>
</body>
</html>
