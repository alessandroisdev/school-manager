<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Configurações da Unidade</h2>
                <p class="text-muted small mb-0">Parâmetros globais para o funcionamento desta franquia.</p>
            </div>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 900px;">
            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Nav tabs -->
                    <div class="col-md-4 mb-4">
                        <div class="nav flex-column nav-pills me-3" id="settings-tabs" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start py-3" id="academic-tab" data-bs-toggle="pill" data-bs-target="#academic" type="button" role="tab" aria-controls="academic" aria-selected="true">
                                <i class="bi bi-mortarboard me-2"></i> Acadêmico
                            </button>
                            <button class="nav-link text-start py-3" id="financial-tab" data-bs-toggle="pill" data-bs-target="#financial" type="button" role="tab" aria-controls="financial" aria-selected="false">
                                <i class="bi bi-cash-coin me-2"></i> Financeiro
                            </button>
                            <button class="nav-link text-start py-3" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding" type="button" role="tab" aria-controls="branding" aria-selected="false">
                                <i class="bi bi-palette me-2"></i> Identidade Visual
                            </button>
                            <button class="nav-link text-start py-3" id="system-tab" data-bs-toggle="pill" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">
                                <i class="bi bi-laptop me-2"></i> Sistema e Portais
                            </button>
                        </div>
                    </div>

                    <!-- Tab content -->
                    <div class="col-md-8 border-start ps-md-4">
                        <div class="tab-content" id="settings-tabContent">
                            <!-- Aba Acadêmico -->
                            <div class="tab-pane fade show active" id="academic" role="tabpanel" aria-labelledby="academic-tab" tabindex="0">
                                <h5 class="mb-4 fw-bold text-dark"><i class="bi bi-mortarboard me-2 text-primary"></i> Parametrização Acadêmica</h5>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Regra de Cálculo de Média *</label>
                                        <select name="settings[calculation_rule]" class="form-select bg-light border-0" required>
                                            <option value="simple" {{ ($settings->calculation_rule ?? '') == 'simple' ? 'selected' : '' }}>Média Simples</option>
                                            <option value="weighted" {{ ($settings->calculation_rule ?? '') == 'weighted' ? 'selected' : '' }}>Média Ponderada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Sistema de Avaliação *</label>
                                        <select name="settings[evaluation_type]" class="form-select bg-light border-0" required>
                                            <option value="bimonthly" {{ ($settings->evaluation_type ?? '') == 'bimonthly' ? 'selected' : '' }}>Bimestral</option>
                                            <option value="trimester" {{ ($settings->evaluation_type ?? '') == 'trimester' ? 'selected' : '' }}>Trimestral</option>
                                            <option value="semester" {{ ($settings->evaluation_type ?? '') == 'semester' ? 'selected' : '' }}>Semestral</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nota de Corte (Aprovação) *</label>
                                        <input type="number" step="0.01" name="settings[passing_grade]" class="form-control bg-light border-0" value="{{ $settings->passing_grade ?? 6.0 }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Frequência Mínima (%) *</label>
                                        <input type="number" step="0.01" name="settings[passing_attendance]" class="form-control bg-light border-0" value="{{ $settings->passing_attendance ?? 75 }}" required>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Lotação Padrão de Turma *</label>
                                        <input type="number" name="settings[default_class_capacity]" class="form-control bg-light border-0" value="{{ $settings->default_class_capacity ?? 30 }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Ano Letivo Vigente *</label>
                                        <input type="number" name="settings[current_academic_year]" class="form-control bg-light border-0" value="{{ $settings->current_academic_year ?? date('Y') }}" required>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Tipo de Chamada *</label>
                                        <select name="settings[attendance_type]" class="form-select bg-light border-0" required>
                                            <option value="daily" {{ ($settings->attendance_type ?? '') == 'daily' ? 'selected' : '' }}>Diária (Uma por dia)</option>
                                            <option value="per_lesson" {{ ($settings->attendance_type ?? '') == 'per_lesson' ? 'selected' : '' }}>Por Aula/Disciplina</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Financeiro -->
                            <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab" tabindex="0">
                                <h5 class="mb-4 fw-bold text-dark"><i class="bi bi-wallet2 me-2 text-success"></i> Parametrização Financeira</h5>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Moeda Padrão *</label>
                                        <select name="settings[currency]" class="form-select bg-light border-0" required>
                                            <option value="BRL" {{ ($settings->currency ?? '') == 'BRL' ? 'selected' : '' }}>Real (BRL)</option>
                                            <option value="USD" {{ ($settings->currency ?? '') == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Dia de Vencimento Padrão *</label>
                                        <input type="number" name="settings[default_due_day]" min="1" max="31" class="form-control bg-light border-0" value="{{ $settings->default_due_day ?? 10 }}" required>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Multa Fixa por Atraso (%) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="settings[late_fee_penalty]" class="form-control bg-light border-0" value="{{ $settings->late_fee_penalty ?? 2.00 }}" required>
                                            <span class="input-group-text border-0 bg-light">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Juros ao Mês (%) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="settings[late_fee_interest]" class="form-control bg-light border-0" value="{{ $settings->late_fee_interest ?? 2.00 }}" required>
                                            <span class="input-group-text border-0 bg-light">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Desconto Pagamento Antecipado (%) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="settings[discount_before_due]" class="form-control bg-light border-0" value="{{ $settings->discount_before_due ?? 0.00 }}" required>
                                            <span class="input-group-text border-0 bg-light">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Identidade Visual -->
                            <div class="tab-pane fade" id="branding" role="tabpanel" aria-labelledby="branding-tab" tabindex="0">
                                <h5 class="mb-4 fw-bold text-dark"><i class="bi bi-palette me-2 text-danger"></i> Identidade Visual da Unidade</h5>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Cor Primária (Hexadecimal) *</label>
                                        <div class="d-flex align-items-center">
                                            <input type="color" name="settings[primary_color]" class="form-control form-control-color bg-light border-0 me-2" value="{{ $settings->primary_color ?? '#0d6efd' }}" title="Escolha a cor da unidade" required>
                                            <span class="text-muted small">Esta cor será aplicada aos relatórios, recibos e portal do aluno.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Cabeçalho de Recibos/Contratos</label>
                                        <textarea name="settings[receipt_header]" class="form-control bg-light border-0" rows="3" placeholder="Ex: Escola Modelo - Unidade Centro&#10;CNPJ: 00.000.000/0001-00">{{ $settings->receipt_header ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Rodapé de Recibos/Contratos</label>
                                        <textarea name="settings[receipt_footer]" class="form-control bg-light border-0" rows="3" placeholder="Ex: Documento gerado pelo sistema SGE.">{{ $settings->receipt_footer ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Sistema -->
                            <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab" tabindex="0">
                                <h5 class="mb-4 fw-bold text-dark"><i class="bi bi-laptop me-2 text-info"></i> Sistema e Integrações</h5>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-bold">Fuso Horário da Unidade *</label>
                                        <select name="settings[timezone]" class="form-select bg-light border-0" required>
                                            <option value="America/Sao_Paulo" {{ ($settings->timezone ?? '') == 'America/Sao_Paulo' ? 'selected' : '' }}>Brasília (America/Sao_Paulo)</option>
                                            <option value="America/Manaus" {{ ($settings->timezone ?? '') == 'America/Manaus' ? 'selected' : '' }}>Manaus (America/Manaus)</option>
                                            <option value="America/Belem" {{ ($settings->timezone ?? '') == 'America/Belem' ? 'selected' : '' }}>Belém (America/Belem)</option>
                                            <option value="America/Fortaleza" {{ ($settings->timezone ?? '') == 'America/Fortaleza' ? 'selected' : '' }}>Fortaleza (America/Fortaleza)</option>
                                        </select>
                                    </div>
                                </div>

                                <h6 class="text-dark fw-bold mb-3 small text-uppercase">Acessos</h6>
                                <div class="mb-3 p-3 bg-light rounded border-0">
                                    <div class="form-check form-switch fs-6">
                                        <input class="form-check-input" type="checkbox" name="settings[enable_student_portal]" value="1" id="enable_student_portal" {{ ($settings->enable_student_portal ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold mt-1 ms-2" for="enable_student_portal">Habilitar Portal do Aluno</label>
                                    </div>
                                    <div class="form-text mt-1 ms-5">Se desativado, os alunos desta unidade não conseguirão fazer login no sistema.</div>
                                </div>
                                <div class="mb-3 p-3 bg-light rounded border-0">
                                    <div class="form-check form-switch fs-6">
                                        <input class="form-check-input" type="checkbox" name="settings[enable_teacher_portal]" value="1" id="enable_teacher_portal" {{ ($settings->enable_teacher_portal ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold mt-1 ms-2" for="enable_teacher_portal">Habilitar Diário do Professor</label>
                                    </div>
                                    <div class="form-text mt-1 ms-5">Se desativado, os professores não poderão lançar notas ou faltas pelo portal web.</div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4 border-secondary">
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                                <i class="bi bi-save me-2"></i> Salvar Configurações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
