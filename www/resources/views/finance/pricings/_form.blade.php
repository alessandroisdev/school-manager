<div class="row g-3">
    <!-- Vinculação Pedagógica -->
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Vinculação Acadêmica</h6>
    </div>

    <div class="col-md-6">
        <label for="grade_id" class="form-label fw-bold">Série <span class="text-danger">*</span></label>
        <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" name="grade_id" required>
            <option value="">Selecione a Série</option>
            @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ old('grade_id', $classPricing->grade_id ?? '') == $grade->id ? 'selected' : '' }}>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>
        @error('grade_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="shift_id" class="form-label fw-bold">Turno <span class="text-danger">*</span></label>
        <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
            <option value="">Selecione o Turno</option>
            @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" {{ old('shift_id', $classPricing->shift_id ?? '') == $shift->id ? 'selected' : '' }}>
                    {{ $shift->name }}
                </option>
            @endforeach
        </select>
        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Regras Financeiras -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Regras Financeiras Anuais</h6>
    </div>

    <div class="col-md-4">
        <label for="annual_amount" class="form-label fw-bold">Valor Total Anual (R$) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">R$</span>
            <input type="number" step="0.01" min="0" class="form-control @error('annual_amount') is-invalid @enderror" id="annual_amount" name="annual_amount" value="{{ old('annual_amount', isset($classPricing) ? $classPricing->annual_amount : '') }}" placeholder="Ex: 12000.00" required>
        </div>
        <div class="form-text">Ex: Valor total do ano letivo.</div>
        @error('annual_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="installments_count" class="form-label fw-bold">Qtd. de Parcelas (Mensalidades) <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" min="1" max="24" class="form-control @error('installments_count') is-invalid @enderror" id="installments_count" name="installments_count" value="{{ old('installments_count', isset($classPricing) ? $classPricing->installments_count : '12') }}" required>
            <span class="input-group-text">parcelas</span>
        </div>
        <div class="form-text">Normalmente 12 meses.</div>
        @error('installments_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="default_due_day" class="form-label fw-bold">Dia Padrão de Vencimento <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Todo dia</span>
            <input type="number" min="1" max="31" class="form-control @error('default_due_day') is-invalid @enderror" id="default_due_day" name="default_due_day" value="{{ old('default_due_day', isset($classPricing) ? $classPricing->default_due_day : '5') }}" required>
        </div>
        <div class="form-text">Ex: Dia 5 de cada mês.</div>
        @error('default_due_day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
