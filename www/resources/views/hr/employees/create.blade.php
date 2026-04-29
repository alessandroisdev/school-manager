<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Novo Colaborador</h2>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('hr.employees.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-badge me-2"></i> Dados do Colaborador</h5>
                
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
                        <label for="position" class="form-label fw-bold text-muted small">Cargo / Função</label>
                        <input type="text" name="position" id="position" class="form-control bg-light border-0 @error('position') is-invalid @enderror" value="{{ old('position') }}" placeholder="Ex: Secretário, Zelador, Diretor">
                        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="hire_date" class="form-label fw-bold text-muted small">Data de Contratação</label>
                        <input type="date" name="hire_date" id="hire_date" class="form-control bg-light border-0 @error('hire_date') is-invalid @enderror" value="{{ old('hire_date') }}">
                        @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
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
                        <i class="bi bi-check-lg me-2"></i> Salvar Colaborador
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
