<x-app-layout>
    <div class="container-fluid">
        
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="glass-card mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h3 class="fw-bold text-dark mb-1">Bem-vindo(a) de volta, {{ Auth::user()->name }}!</h3>
                    <p class="text-muted mb-0">O painel de controle do SGE está pronto para uso.</p>
                </div>
                
                <div class="text-md-end">
                    <p class="small text-muted mb-1 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Unidade Operacional Ativa</p>
                    <p class="h5 fw-black text-primary mb-0">
                        {{ Auth::user()->units->where('id', session('active_unit_id'))->first()?->name ?? 'Nenhuma unidade configurada' }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center h-100 p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0 text-uppercase">Alunos Ativos</p>
                        <h3 class="fw-black text-dark mb-0">1.204</h3>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center h-100 p-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0 text-uppercase">Frequência Hoje</p>
                        <h3 class="fw-black text-dark mb-0">96.5%</h3>
                    </div>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="glass-card d-flex align-items-center h-100 p-4">
                    <div class="bg-purple bg-opacity-10 text-purple rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0 text-uppercase">Bimestre Ativo</p>
                        <h3 class="fw-black text-dark mb-0">2º Bim</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
