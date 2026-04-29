<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Diários de Classe</h2>
                <p class="text-muted small mb-0">Selecione uma turma para lançar Frequência ou Notas.</p>
            </div>
            <button class="btn btn-warning fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;" data-bs-toggle="collapse" data-bs-target="#insightsPanel">
                <i class="bi bi-robot me-1"></i> Teacher Insights (IA)
            </button>
        </div>

        <div class="collapse mb-4" id="insightsPanel">
            <div class="glass-card p-4 border-warning border-start border-4">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-lightbulb-fill me-2"></i> Análise Preditiva e Destaques</h5>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <h6 class="fw-bold text-danger small text-uppercase mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Alunos em Risco</h6>
                        <ul class="list-group list-group-flush small">
                            @foreach($insights['at_risk'] as $risk)
                                <li class="list-group-item bg-transparent px-0 py-2 border-0">
                                    <strong class="text-dark">{{ $risk['name'] }}</strong> <span class="text-muted">({{ $risk['class'] }})</span><br>
                                    <span class="text-danger"><i class="bi bi-arrow-down-right"></i> {{ $risk['reason'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="fw-bold text-success small text-uppercase mb-2"><i class="bi bi-star-fill me-1"></i> Destaques Positivos</h6>
                        <ul class="list-group list-group-flush small">
                            @foreach($insights['highlights'] as $high)
                                <li class="list-group-item bg-transparent px-0 py-2 border-0">
                                    <strong class="text-dark">{{ $high['name'] }}</strong> <span class="text-muted">({{ $high['class'] }})</span><br>
                                    <span class="text-success"><i class="bi bi-graph-up-arrow"></i> {{ $high['reason'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-warning bg-opacity-10 rounded h-100 border border-warning border-opacity-25">
                            <h6 class="fw-bold text-warning small text-uppercase mb-2"><i class="bi bi-magic me-1"></i> Sugestão da IA</h6>
                            <p class="small text-dark mb-0">{{ $insights['suggestion'] }}</p>
                            <button class="btn btn-sm btn-outline-warning mt-3 fw-bold w-100"><i class="bi bi-envelope-paper me-1"></i> Agendar Reunião</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'diary_name', 'label' => 'Turma e Disciplina'],
                    ['name' => 'teacher_name', 'label' => 'Professor Responsável', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Acesso ao Diário', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <div class="glass-card p-4">
                <x-datatable id="diariesTable" url="{{ route('academic.diary.index') }}" :columns="$columns" />
            </div>
        </div>
    </div>
</x-app-layout>
