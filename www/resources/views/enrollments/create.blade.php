<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Nova Matrícula (Enturmação)</h2>
            <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('enrollments.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-link-45deg me-2"></i> Vincular Aluno à Turma</h5>
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="student_id" class="form-label fw-bold text-muted small">Selecione o Aluno *</label>
                        <select name="student_id" id="student_id" class="form-select form-select-lg bg-light border-0 fw-bold @error('student_id') is-invalid @enderror" required>
                            <option value="">Buscar aluno (digite para filtrar)...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} - CPF: {{ $student->document }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="school_class_id" class="form-label fw-bold text-muted small">Selecione a Turma * (Apenas com vagas)</label>
                        <select name="school_class_id" id="school_class_id" class="form-select form-select-lg bg-light border-0 fw-bold @error('school_class_id') is-invalid @enderror" required>
                            <option value="">Selecione a Turma de destino...</option>
                            @foreach($classes as $class)
                                @php
                                    $available = $class->capacity - $class->enrollments_count;
                                    $isFull = $available <= 0;
                                @endphp
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                    {{ $class->name }} ({{ $class->grade->name }} / {{ $class->shift->name }}) - 
                                    @if($isFull)
                                        [TURMA LOTADA]
                                    @else
                                        {{ $available }} Vagas de {{ $class->capacity }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label for="status" class="form-label fw-bold text-muted small">Status da Matrícula</label>
                        <select name="status" id="status" class="form-select bg-light border-0 @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativa / Trancada</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-check-lg me-2"></i> Efetivar Matrícula
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script rápido para habilitar Select2 se quisermos no futuro -->
    <script type="module">
        import $ from 'jquery';
        // import 'select2'; // se estivesse instalado no npm
        // $('#student_id').select2({ theme: 'bootstrap-5' });
    </script>
</x-app-layout>
