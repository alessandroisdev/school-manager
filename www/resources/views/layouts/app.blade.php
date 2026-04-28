<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGE - Sistema de Gestão Escolar</title>
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
</head>
<body class="d-flex flex-column min-vh-100">
    @auth
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                    <i class="bi bi-mortarboard-fill me-2"></i> SGE
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button class="btn nav-link" type="submit">Sair <i class="bi bi-box-arrow-right"></i></button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endauth

    <main class="flex-grow-1 d-flex">
        @yield('content')
    </main>

    <footer class="bg-body-tertiary text-center py-3 mt-auto">
        <small class="text-muted">© {{ date('Y') }} SGE - Sistema de Gestão Escolar</small>
    </footer>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            });
        </script>
    @endif
    
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.Toast.fire({
                    icon: 'error',
                    title: "{{ $errors->first() }}"
                });
            });
        </script>
    @endif
</body>
</html>
