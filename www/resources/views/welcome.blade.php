<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Colégio SGE - Construindo o Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 8rem 0 5rem 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            opacity: 0.15;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .feature-icon {
            width: 60px; height: 60px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 1rem;
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent position-absolute w-100" style="z-index: 10;">
        <div class="container py-3">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>SGE School
            </a>
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-light fw-bold px-4 rounded-pill">Área Restrita</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="badge bg-primary bg-opacity-25 text-light px-3 py-2 rounded-pill mb-3 border border-primary border-opacity-50">Matrículas Abertas 2026</span>
                    <h1 class="display-3 fw-bold mb-4" style="letter-spacing: -1px;">Educação que <span class="text-primary">Transforma</span> Vidas</h1>
                    <p class="lead mb-5 text-light opacity-75">Prepare seu filho para os desafios do futuro com nossa metodologia de ensino inovadora, estrutura de ponta e corpo docente de excelência.</p>
                    
                    <div class="d-flex flex-wrap gap-4">
                        <div>
                            <h4 class="fw-bold mb-0">15+</h4>
                            <p class="small text-light opacity-50 mb-0">Anos de Tradição</p>
                        </div>
                        <div class="border-start border-secondary ps-4">
                            <h4 class="fw-bold mb-0">98%</h4>
                            <p class="small text-light opacity-50 mb-0">Aprovação nas Federais</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="glass-card p-5">
                        <h4 class="fw-bold text-dark mb-2">Demonstre Interesse</h4>
                        <p class="text-muted small mb-4">Preencha o formulário e nossa equipe pedagógica entrará em contato.</p>
                        
                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm py-2"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm py-2">Verifique os campos obrigatórios.</div>
                        @endif

                        <form action="{{ route('public.lead.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Seu Nome (Responsável)</label>
                                <input type="text" name="parent_name" class="form-control bg-light border-0 py-2" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Telefone / WhatsApp</label>
                                    <input type="text" name="phone" class="form-control bg-light border-0 py-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">E-mail</label>
                                    <input type="email" name="email" class="form-control bg-light border-0 py-2">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nome do Aluno</label>
                                <input type="text" name="student_name" class="form-control bg-light border-0 py-2" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Série de Interesse</label>
                                <select name="grade_id" class="form-select bg-light border-0 py-2" required>
                                    <option value="">Selecione...</option>
                                    @foreach($grades as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow-sm">
                                Solicitar Contato <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Por que escolher o SGE?</h2>
                <p class="text-muted">Infraestrutura e metodologia projetadas para o sucesso do aluno.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 h-100 shadow-sm text-center p-4">
                        <div><div class="feature-icon"><i class="bi bi-laptop"></i></div></div>
                        <h5 class="fw-bold text-dark">Tecnologia em Sala</h5>
                        <p class="text-muted small">Salas equipadas com lousas digitais e acompanhamento 100% online pelo Portal do Aluno.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100 shadow-sm text-center p-4">
                        <div><div class="feature-icon"><i class="bi bi-globe-americas"></i></div></div>
                        <h5 class="fw-bold text-dark">Bilinguismo</h5>
                        <p class="text-muted small">Carga horária estendida de inglês com metodologia internacional certificada.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100 shadow-sm text-center p-4">
                        <div><div class="feature-icon"><i class="bi bi-heart-pulse"></i></div></div>
                        <h5 class="fw-bold text-dark">Sócio Emocional</h5>
                        <p class="text-muted small">Acompanhamento psicológico e projetos de desenvolvimento humano integrados.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-light py-4 text-center">
        <p class="mb-0 small text-muted">&copy; {{ date('Y') }} SGE School Manager. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
