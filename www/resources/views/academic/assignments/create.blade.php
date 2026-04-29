<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Nova Alocação Docente</h2>
            <a href="{{ route('academic.assignments.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('academic.assignments.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-info"><i class="bi bi-diagram-3-fill me-2"></i> Designar Professor para Turma/Matéria</h5>
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="teacher_id" class="form-label fw-bold text-muted small">Selecione o Professor *</label>
                        <select name="teacher_id" id="teacher_id" class="form-select form-select-lg bg-light border-0 fw-bold @error('teacher_id') is-invalid @enderror" required>
                            <option value="">Buscar professor...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->employee->name }} {{ $teacher->specialty ? '('.$teacher->specialty.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="school_class_id" class="form-label fw-bold text-muted small">Selecione a Turma *</label>
                        <select name="school_class_id" id="school_class_id" class="form-select form-select-lg bg-light border-0 fw-bold @error('school_class_id') is-invalid @enderror" required>
                            <option value="">Selecione a Turma...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->grade->name }} / {{ $class->shift->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label for="subject_id" class="form-label fw-bold text-muted small">Selecione a Disciplina *</label>
                        <select name="subject_id" id="subject_id" class="form-select form-select-lg bg-light border-0 fw-bold @error('subject_id') is-invalid @enderror" required>
                            <option value="">Selecione a Matéria...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} {{ $subject->code ? '['.$subject->code.']' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="assigned_workload" class="form-label fw-bold text-muted small">Carga H. Semanal *</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="assigned_workload" id="assigned_workload" class="form-control bg-light border-0 @error('assigned_workload') is-invalid @enderror" value="{{ old('assigned_workload', 2) }}" required min="1" max="160">
                            <span class="input-group-text bg-light border-0">h</span>
                        </div>
                        @error('assigned_workload')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-check-lg me-2"></i> Efetivar Alocação
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
