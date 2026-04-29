<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Editar Turma</h2>
            <a href="{{ route('academic.classes.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 p-md-5 mx-auto" style="max-width: 800px;">
            <form action="{{ route('academic.classes.update', $class) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-bold text-muted">Nome da Turma</label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name', $class->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="capacity" class="form-label fw-bold text-muted">Capacidade Máxima</label>
                        <input type="number" name="capacity" id="capacity" class="form-control form-control-lg bg-light border-0 @error('capacity') is-invalid @enderror" value="{{ old('capacity', $class->capacity) }}" min="1" max="100" required>
                        @error('capacity')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label for="academic_year_id" class="form-label fw-bold text-muted">Ano Letivo</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select form-select-lg bg-light border-0 @error('academic_year_id') is-invalid @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $class->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="grade_id" class="form-label fw-bold text-muted">Série / Ano</label>
                        <select name="grade_id" id="grade_id" class="form-select form-select-lg bg-light border-0 @error('grade_id') is-invalid @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id', $class->grade_id) == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="shift_id" class="form-label fw-bold text-muted">Turno</label>
                        <select name="shift_id" id="shift_id" class="form-select form-select-lg bg-light border-0 @error('shift_id') is-invalid @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id', $class->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Atualizar Turma
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
