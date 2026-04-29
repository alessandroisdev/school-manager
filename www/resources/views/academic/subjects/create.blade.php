<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Nova Disciplina</h2>
            <a href="{{ route('academic.subjects.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('academic.subjects.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-book-half me-2"></i> Informações da Disciplina</h5>
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-bold text-muted small">Nome da Disciplina *</label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Ex: Matemática, História, Artes">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label fw-bold text-muted small">Código Interno</label>
                        <input type="text" name="code" id="code" class="form-control form-control-lg bg-light border-0 @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Ex: MAT01">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="workload" class="form-label fw-bold text-muted small">Carga Horária Base (Horas)</label>
                        <div class="input-group">
                            <input type="number" name="workload" id="workload" class="form-control bg-light border-0 @error('workload') is-invalid @enderror" value="{{ old('workload') }}" min="1">
                            <span class="input-group-text bg-light border-0">h</span>
                        </div>
                        @error('workload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="is_active" class="form-label fw-bold text-muted small">Status</label>
                        <select name="is_active" id="is_active" class="form-select bg-light border-0 @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Ativa</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inativa</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-12">
                        <label for="description" class="form-label fw-bold text-muted small">Descrição Curta / Ementa Base</label>
                        <textarea name="description" id="description" rows="3" class="form-control bg-light border-0 @error('description') is-invalid @enderror" placeholder="Descrição opcional dos objetivos principais desta matéria...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-check-lg me-2"></i> Salvar Disciplina
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
