<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SGE') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .sidebar { height: 100vh; max-height: 100vh; background-color: #1e293b; color: #fff; width: 280px; transition: all 0.3s; display: flex; flex-direction: column; overflow: hidden; }
        .sidebar-scrollable { flex-grow: 1; overflow-y: auto; overflow-x: hidden; padding-right: 5px; }
        .sidebar-scrollable::-webkit-scrollbar { width: 6px; }
        .sidebar-scrollable::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollable::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: 0.5rem; margin-bottom: 0.25rem; padding: 0.75rem 1rem; font-weight: 500; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #334155; color: #fff; transform: translateX(4px); }
        .sidebar-brand { font-size: 1.5rem; font-weight: 900; color: #fff; letter-spacing: -0.5px; }
        .main-content { flex-grow: 1; padding: 2rem; overflow-y: auto; height: 100vh; }
        .topbar { background: #fff; border: 1px solid #e2e8f0; padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .glass-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 1.5rem; }
        .table-responsive { border-radius: 0.5rem; }
    </style>
</head>
<body class="d-flex overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="sidebar p-3 d-flex flex-column shadow">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-4 text-decoration-none px-3 mt-2">
            <i class="bi bi-hexagon-fill fs-3 text-primary me-2"></i>
            <span class="sidebar-brand">SGE ERP</span>
        </a>
        <hr class="border-secondary mt-0">
        
        <div class="sidebar-scrollable">
            <ul class="nav nav-pills flex-column mb-auto">
                
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor') || auth()->user()->hasRole('secretaria'))
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid me-2"></i> Dashboard Geral
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('aluno'))
                <li class="nav-item mt-2">
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge me-2"></i> Meu Portal
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Gestão Global</h6>
                <li>
                    <a href="{{ route('admin.units.index') }}" class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                        <i class="bi bi-building me-2"></i> Gestão de Franquias
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill me-2"></i> Configurações Globais
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor') || auth()->user()->hasRole('secretaria'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Secretaria Central</h6>
                
                <!-- NOVO MENU: Captação de Leads -->
                <li>
                    <a href="{{ route('secretariat.leads.index') }}" class="nav-link {{ request()->routeIs('secretariat.leads.*') ? 'active' : '' }}">
                        <i class="bi bi-funnel-fill me-2 text-warning"></i> Captação / Leads
                    </a>
                </li>

                <li>
                    <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill me-2"></i> Gestão de Alunos
                    </a>
                </li>
                <li>
                    <a href="{{ route('enrollments.index') }}" class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                        <i class="bi bi-link-45deg me-2"></i> Matrículas
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Módulo Pedagógico</h6>
                <li>
                    <a href="{{ route('academic.smart.index') }}" class="nav-link {{ request()->routeIs('academic.smart.*') ? 'active' : '' }}">
                        <i class="bi bi-magic me-2 text-warning"></i> Enturmação em Lote (IA)
                    </a>
                </li>
                <li>
                    <a href="{{ route('academic.grades.index') }}" class="nav-link {{ request()->routeIs('academic.grades.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard me-2"></i> Séries e Anos
                    </a>
                </li>
                <li>
                    <a href="{{ route('academic.shifts.index') }}" class="nav-link {{ request()->routeIs('academic.shifts.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i> Turnos
                    </a>
                </li>
                <li>
                    <a href="{{ route('academic.classes.index') }}" class="nav-link {{ request()->routeIs('academic.classes.*') ? 'active' : '' }}">
                        <i class="bi bi-door-open me-2"></i> Gestão de Turmas
                    </a>
                </li>
                <li>
                    <a href="{{ route('academic.subjects.index') }}" class="nav-link {{ request()->routeIs('academic.subjects.*') ? 'active' : '' }}">
                        <i class="bi bi-book-half me-2"></i> Disciplinas
                    </a>
                </li>
                <li>
                    <a href="{{ route('academic.assignments.index') }}" class="nav-link {{ request()->routeIs('academic.assignments.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill me-2"></i> Alocação Docente
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor') || auth()->user()->hasRole('professor'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Ambiente de Aula</h6>
                <li>
                    <a href="{{ route('academic.diary.index') }}" class="nav-link {{ request()->routeIs('academic.diary.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text me-2"></i> Diário do Professor
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Recursos Humanos</h6>
                <li>
                    <a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard me-2"></i> Colaboradores
                    </a>
                </li>
                <li>
                    <a href="{{ route('hr.teachers.index') }}" class="nav-link {{ request()->routeIs('hr.teachers.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-bookmark-fill me-2"></i> Corpo Docente
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('diretor'))
                <h6 class="sidebar-heading px-3 mt-4 mb-2 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">Financeiro</h6>
                <li>
                    <a href="{{ route('finance.invoices.index') }}" class="nav-link {{ request()->routeIs('finance.invoices.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin me-2"></i> Faturamento
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <hr class="border-secondary mt-0">

        <hr class="border-secondary">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>{{ Auth::user()->name }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow w-100" aria-labelledby="dropdownUser1">
                @if(auth()->user()->hasRole('admin'))
                <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                <li><hr class="dropdown-divider"></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sair</button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark fw-bold text-muted"><i class="bi bi-buildings me-2"></i> Unidade de Ensino</h5>
            <div class="d-flex align-items-center">
                @php
                    $availableUnits = auth()->user()->hasRole('admin') ? \App\Domains\Shared\Models\Unit::all() : auth()->user()->units;
                @endphp
                
                @if($availableUnits->count() > 1)
                    <form method="POST" action="{{ route('context.switch') }}" class="m-0">
                        @csrf
                        <select name="unit_id" onchange="this.form.submit()" class="form-select form-select-sm border-0 bg-primary bg-opacity-10 fw-bold text-primary rounded-pill px-4 py-2 cursor-pointer shadow-sm" style="appearance: auto; padding-right: 2.5rem;">
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}" {{ session('active_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @elseif($availableUnits->count() == 1)
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-4 py-2 fs-6 shadow-sm border border-primary border-opacity-25">{{ $availableUnits->first()->name }}</span>
                @else
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-4 py-2 fs-6 shadow-sm">Sem Vínculo</span>
                @endif
            </div>
        </header>

        <!-- Slot Content -->
        {{ $slot }}
    </main>
    
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                window.Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                window.Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if ($errors->any())
                window.Toast.fire({
                    icon: 'error',
                    title: "Existem erros de validação!"
                });
            @endif
        });
    </script>
</body>
</html>
