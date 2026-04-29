<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Novo Turno</h2>
            <a href="{{ route('academic.shifts.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 p-md-5 mx-auto" style="max-width: 600px;">
            <form action="{{ route('academic.shifts.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="form-label fw-bold text-muted">Nome do Turno (Ex: Matutino, Vespertino)</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-bold text-muted">Horário de Entrada</label>
                        <input type="time" name="start_time" id="start_time" class="form-control form-control-lg bg-light border-0 @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_time" class="form-label fw-bold text-muted">Horário de Saída</label>
                        <input type="time" name="end_time" id="end_time" class="form-control form-control-lg bg-light border-0 @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Salvar Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
