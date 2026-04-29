<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Novo Professor</h2>
            <a href="{{ route('hr.teachers.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('hr.teachers.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-info"><i class="bi bi-journal-bookmark-fill me-2"></i> Cadastro Docente Integrado</h5>
                <p class="text-muted small mb-4">Ao preencher este formulário, o sistema criará o registro base de Colaborador e o perfil especializado de Professor automaticamente.</p>
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-bold text-muted small">Nome Completo *</label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="document" class="form-label fw-bold text-muted small">Documento (CPF/RG) *</label>
                        <input type="text" name="document" id="document" class="form-control bg-light border-0 @error('document') is-invalid @enderror" value="{{ old('document') }}" required>
                        @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="specialty" class="form-label fw-bold text-muted small">Especialidade Principal</label>
                        <input type="text" name="specialty" id="specialty" class="form-control bg-light border-0 @error('specialty') is-invalid @enderror" value="{{ old('specialty') }}" placeholder="Ex: Matemática, Letras, Física">
                        @error('specialty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="max_workload" class="form-label fw-bold text-muted small">Carga Horária Máx. *</label>
                        <div class="input-group">
                            <input type="number" name="max_workload" id="max_workload" class="form-control bg-light border-0 @error('max_workload') is-invalid @enderror" value="{{ old('max_workload', 40) }}" required min="1" max="160">
                            <span class="input-group-text bg-light border-0">h/sem</span>
                        </div>
                        @error('max_workload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="hire_date" class="form-label fw-bold text-muted small">Data de Contratação</label>
                        <input type="date" name="hire_date" id="hire_date" class="form-control bg-light border-0 @error('hire_date') is-invalid @enderror" value="{{ old('hire_date') }}">
                        @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="is_active" class="form-label fw-bold text-muted small">Status</label>
                        <select name="is_active" id="is_active" class="form-select bg-light border-0 @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-check-lg me-2"></i> Salvar Professor
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
