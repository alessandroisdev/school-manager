<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Alocação de Professores (Kanban)</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('academic.assignments.print') }}" class="btn btn-outline-dark fw-bold shadow-sm" target="_blank">
                    <i class="bi bi-printer me-1"></i> Imprimir Cronograma
                </a>
                @if($hasDrafts)
                    <form action="{{ route('academic.assignments.clear') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-bold shadow-sm">
                            <i class="bi bi-eraser me-1"></i> Limpar Rascunho
                        </button>
                    </form>
                    <form action="{{ route('academic.assignments.publish') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Publicar Grade
                        </button>
                    </form>
                    <form action="{{ route('academic.assignments.generate') }}" method="POST" class="d-inline mt-2 mt-md-0 ms-0 ms-md-2">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm" title="Gerar Quadro de Horários">
                            <i class="bi bi-calendar3 me-1"></i> Gerar Horários
                        </button>
                    </form>
                @else
                    <form action="{{ route('academic.assignments.auto-allocate') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                            <i class="bi bi-magic me-1"></i> IA Alocação Geral
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fw-bold"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}</div>
        @endif
        @if(session('warning_list'))
            <div class="alert alert-warning shadow-sm border-warning border-opacity-25">
                <p class="fw-bold mb-2 text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Ocorreram ressalvas durante a geração da Grade:</p>
                <ul class="mb-0 small text-dark" style="max-height: 200px; overflow-y: auto;">
                    @foreach(session('warning_list') as $warn)
                        <li class="mb-1">{{ $warn }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- Sidebar: Matérias e IA -->
            <div class="col-md-3">
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-book me-2"></i> Disciplinas</h5>
                    <p class="small text-muted mb-3">Arraste a disciplina para a turma desejada na linha do professor.</p>
                    <div id="subject-list" class="d-flex flex-column gap-2" style="min-height: 100px;">
                        @foreach($subjects as $subject)
                            <div class="card border border-primary border-opacity-25 shadow-sm p-2 bg-white draggable-subject" 
                                 data-subject-id="{{ $subject->id }}" 
                                 draggable="true">
                                <div class="fw-bold text-primary" style="font-size: 0.85rem;"><i class="bi bi-grip-vertical text-muted"></i> {{ $subject->name }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="glass-card p-4 border-info border-opacity-25">
                    <h5 class="fw-bold text-info mb-3"><i class="bi bi-robot me-2"></i> IA Sugestões</h5>
                    
                    @if(isset($suggestions['teachers']))
                        <div class="mb-3">
                            <h6 class="fw-bold small text-dark">Professores Ociosos</h6>
                            <ul class="list-unstyled small mb-0">
                                @foreach($suggestions['teachers'] as $sugT)
                                    <li class="mb-1 text-muted"><i class="bi bi-person-fill text-warning me-1"></i> {{ $sugT['name'] }} (Falta {{ $sugT['available_hours'] }}h)</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($suggestions['classes']))
                        <div>
                            <h6 class="fw-bold small text-dark">Turmas com Déficit</h6>
                            <ul class="list-unstyled small mb-0">
                                @foreach($suggestions['classes'] as $sugC)
                                    <li class="mb-2 text-muted border-bottom pb-1">
                                        <i class="bi bi-door-open-fill text-danger me-1"></i> {{ $sugC['name'] }} <br>
                                        <span class="text-danger" style="font-size: 0.7rem;">Faltam: {{ implode(', ', $sugC['missing_subjects']) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i> Nenhuma turma com déficit crítico.</p>
                    @endif
                </div>
            </div>

            <!-- Kanban Principal -->
            <div class="col-md-9">
                <div class="glass-card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" style="min-width: 1000px;">
                            <thead class="bg-body-secondary">
                                <tr>
                                    <th class="p-3" style="width: 250px;">Professores</th>
                                    @foreach($shifts as $shift)
                                        @php $shiftClasses = $classes->where('shift_id', $shift->id); @endphp
                                        @if($shiftClasses->count() > 0)
                                            <th colspan="{{ $shiftClasses->count() }}" class="text-center p-3 border-start border-2 border-secondary bg-body-tertiary">
                                                Turno: {{ $shift->name }}
                                            </th>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr>
                                    <th></th>
                                    @foreach($shifts as $shift)
                                        @foreach($classes->where('shift_id', $shift->id) as $class)
                                            <th class="text-center small text-muted border-start {{ $loop->first ? 'border-2 border-secondary' : '' }}">
                                                {{ $class->name }} <br><span style="font-size: 0.7rem;">{{ $class->grade->name }}</span>
                                            </th>
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $teacher)
                                    <tr>
                                        <td class="p-3 align-middle">
                                            <div class="fw-bold">{{ $teacher->employee->name }}</div>
                                            <div class="small text-muted">{{ $teacher->specialty ?? 'Geral' }} | Max: {{ $teacher->max_workload }}h</div>
                                            <form action="{{ route('academic.assignments.generate') }}" method="POST" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-primary py-0" style="font-size: 0.7rem;">Alocar Automático</button>
                                            </form>
                                        </td>
                                        @foreach($shifts as $shift)
                                            @foreach($classes->where('shift_id', $shift->id) as $class)
                                                <td class="p-2 kanban-dropzone border-start {{ $loop->first ? 'border-2 border-secondary' : '' }}" 
                                                    data-teacher-id="{{ $teacher->id }}" 
                                                    data-class-id="{{ $class->id }}"
                                                    style="min-width: 120px; vertical-align: top;">
                                                    
                                                    @php
                                                        $assignments = $teacher->assignments->where('school_class_id', $class->id);
                                                    @endphp

                                                    @foreach($assignments as $assignment)
                                                        <div class="card border-0 shadow-sm p-2 mb-2 {{ $assignment->status == 'draft' ? 'bg-warning text-dark' : 'bg-body' }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold" style="font-size: 0.75rem;">{{ $assignment->subject->name ?? 'N/A' }}</span>
                                                                @if($assignment->status == 'draft')
                                                                    <span class="badge bg-warning text-dark px-1 py-0" title="Rascunho"><i class="bi bi-clock"></i></span>
                                                                @else
                                                                    <span class="badge bg-success px-1 py-0" title="Publicado"><i class="bi bi-check"></i></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para Drag and Drop Native -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const draggables = document.querySelectorAll('.draggable-subject');
            const dropzones = document.querySelectorAll('.kanban-dropzone');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                });
                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                });
            });

            dropzones.forEach(zone => {
                zone.addEventListener('dragover', e => {
                    e.preventDefault();
                    zone.classList.add('bg-primary');
                    zone.classList.add('bg-opacity-10');
                });
                zone.addEventListener('dragleave', () => {
                    zone.classList.remove('bg-primary');
                    zone.classList.remove('bg-opacity-10');
                });
                zone.addEventListener('drop', e => {
                    e.preventDefault();
                    zone.classList.remove('bg-primary');
                    zone.classList.remove('bg-opacity-10');

                    const draggable = document.querySelector('.dragging');
                    if (!draggable) return;

                    const subjectId = draggable.getAttribute('data-subject-id');
                    const teacherId = zone.getAttribute('data-teacher-id');
                    const classId = zone.getAttribute('data-class-id');

                    // Criar elemento visual temporário
                    const clone = draggable.cloneNode(true);
                    clone.classList.remove('dragging');
                    clone.classList.remove('draggable-subject');
                    clone.classList.add('bg-warning', 'bg-opacity-25', 'border-0', 'mb-2');
                    zone.appendChild(clone);

                    // Requisicao AJAX para salvar
                    fetch('{{ route('academic.assignments.move') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            teacher_id: teacherId,
                            school_class_id: classId,
                            subject_id: subjectId
                        })
                    }).then(response => {
                        if (response.ok) {
                            // Salvo com sucesso no draft
                            window.location.reload();
                        } else {
                            response.json().then(data => {
                                alert(data.message || 'Erro ao alocar disciplina.');
                            }).catch(() => {
                                alert('Erro ao processar resposta do servidor.');
                            });
                            clone.remove();
                        }
                    }).catch(() => {
                        alert('Erro de conexão ao alocar.');
                        clone.remove();
                    });
                });
            });
        });
    </script>
</x-app-layout>
