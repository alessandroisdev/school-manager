<div class="row g-3">
    <!-- Informações Básicas -->
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Identificação</h6>
    </div>

    <div class="col-md-8">
        <label for="name" class="form-label fw-bold">Nome de Identificação <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $bankAccount->name ?? '') }}" placeholder="Ex: Itaú Principal" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    
    <div class="col-md-4">
        <label for="bank_code" class="form-label fw-bold">Cód. do Banco <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('bank_code') is-invalid @enderror" id="bank_code" name="bank_code" value="{{ old('bank_code', $bankAccount->bank_code ?? '') }}" placeholder="Ex: 341" required>
        @error('bank_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Dados Bancários -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Dados Bancários</h6>
    </div>

    <div class="col-md-4">
        <label for="agency" class="form-label fw-bold">Agência (com dígito se houver) <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('agency') is-invalid @enderror" id="agency" name="agency" value="{{ old('agency', $bankAccount->agency ?? '') }}" placeholder="Ex: 1234-5" required>
        @error('agency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="account" class="form-label fw-bold">Conta Corrente (com dígito) <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('account') is-invalid @enderror" id="account" name="account" value="{{ old('account', $bankAccount->account ?? '') }}" placeholder="Ex: 12345-6" required>
        @error('account')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="wallet" class="form-label fw-bold">Carteira</label>
        <input type="text" class="form-control @error('wallet') is-invalid @enderror" id="wallet" name="wallet" value="{{ old('wallet', $bankAccount->wallet ?? '') }}" placeholder="Ex: 109">
        @error('wallet')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <!-- Taxas e Juros -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Juros, Multas e Instruções do Boleto</h6>
    </div>

    <div class="col-md-6">
        <label for="fine_percentage" class="form-label fw-bold">Multa Atraso (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('fine_percentage') is-invalid @enderror" id="fine_percentage" name="fine_percentage" value="{{ old('fine_percentage', isset($bankAccount) ? $bankAccount->fine_percentage : '2.00') }}" required>
        @error('fine_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="interest_percentage" class="form-label fw-bold">Juros Mora ao Mês (%) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('interest_percentage') is-invalid @enderror" id="interest_percentage" name="interest_percentage" value="{{ old('interest_percentage', isset($bankAccount) ? $bankAccount->interest_percentage : '1.00') }}" required>
        @error('interest_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="instruction_lines" class="form-label fw-bold">Linhas de Instrução Adicionais</label>
        <textarea class="form-control @error('instruction_lines') is-invalid @enderror" id="instruction_lines" name="instruction_lines" rows="3" placeholder="Ex: Não receber após 30 dias do vencimento.">{{ old('instruction_lines', $bankAccount->instruction_lines ?? '') }}</textarea>
        @error('instruction_lines')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4">
        <div class="form-check form-switch fs-5">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', isset($bankAccount) ? $bankAccount->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label ms-2 mt-1 fs-6 fw-bold" for="is_active">Conta Bancária Ativa</label>
        </div>
        <div class="form-text mt-1">Apenas contas ativas podem ser selecionadas para gerar boletos.</div>
    </div>
</div>
