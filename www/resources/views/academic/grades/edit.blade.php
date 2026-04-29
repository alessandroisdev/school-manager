<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Editar Série / Ano Escolar</h2>
            <a href="{{ route('academic.grades.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 p-md-5 mx-auto" style="max-width: 600px;">
            <form action="{{ route('academic.grades.update', $grade) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="name" class="form-label fw-bold text-muted">Nome da Série</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name', $grade->name) }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Atualizar Série
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
