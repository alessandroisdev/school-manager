<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SGE') }} - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .login-card { border: none; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .brand-logo { font-weight: 900; letter-spacing: -1px; font-size: 3rem; }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-light min-vh-100">
    <main class="w-100 m-auto" style="max-width: 450px;">
        <div class="text-center mb-4">
            <h1 class="brand-logo text-primary">SGE<span class="text-dark">.</span></h1>
            <p class="text-muted">Sistema de Gestão Escolar</p>
        </div>

        <div class="card login-card p-4">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
        
        <p class="mt-5 mb-3 text-muted text-center">&copy; {{ date('Y') }} SGE Solutions</p>
    </main>
</body>
</html>
