<x-app-layout>
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary me-3 shadow-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            <h2 class="h3 mb-0 text-dark fw-bold">Editar Matrícula: {{ $enrollment->student->name }}</h2>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Turma Atual</label>
                                <input type="text" class="form-control" value="{{ $enrollment->schoolClass->name }} - {{ $enrollment->schoolClass->shift->name }}" readonly disabled>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status da Matrícula <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $enrollment->status->value ?? $enrollment->status) == 'active' ? 'selected' : '' }}>Ativa</option>
                                    <option value="inactive" {{ old('status', $enrollment->status->value ?? $enrollment->status) == 'inactive' ? 'selected' : '' }}>Trancada</option>
                                    <option value="transferred" {{ old('status', $enrollment->status->value ?? $enrollment->status) == 'transferred' ? 'selected' : '' }}>Transferida</option>
                                    <option value="completed" {{ old('status', $enrollment->status->value ?? $enrollment->status) == 'completed' ? 'selected' : '' }}>Concluída</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-currency-dollar"></i> Exceções Financeiras da Matrícula</h6>
                                <p class="text-muted small">Preencha apenas se este aluno possuir uma condição financeira diferente do padrão da turma (bolsa ou boleto em banco específico).</p>
                            </div>

                            <div class="col-md-6">
                                <label for="bank_account_id" class="form-label fw-bold">Banco Específico para Carnê</label>
                                <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
                                    <option value="">-- Usar Banco Padrão da Escola --</option>
                                    @foreach($bankAccounts as $bank)
                                        <option value="{{ $bank->id }}" {{ old('bank_account_id', $enrollment->bank_account_id) == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->name }} ({{ $bank->bank_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="discount_percentage" class="form-label fw-bold">Bolsa / Desconto (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control @error('discount_percentage') is-invalid @enderror" id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage', $enrollment->discount_percentage ?? 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text">Ex: 100 para isenção total.</div>
                                @error('discount_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-check-lg me-1"></i> Salvar Matrícula</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="glass-card p-4 bg-primary bg-opacity-10 border-0 h-100">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i> Dicas</h5>
                    <p class="text-muted small"><strong>Bolsas 100%:</strong> Ao configurar o Desconto como 100%, os carnês desse aluno sairão com valor zerado e não farão parte do faturamento a receber da escola.</p>
                    <p class="text-muted small"><strong>Banco Específico:</strong> Caso a escola possua mais de uma conta (ex: Caixa para alunos regulares, Banco do Brasil para integrais), você pode direcionar os carnês deste aluno especificamente.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
