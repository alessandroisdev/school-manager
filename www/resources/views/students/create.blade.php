<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Novo Aluno</h2>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <form action="{{ route('students.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <!-- Coluna Principal: Dados Pessoais e Endereço -->
                <div class="col-lg-8">
                    
                    <!-- Dados Pessoais -->
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-badge me-2"></i> Dados Pessoais</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold text-muted small">Nome Completo do Aluno *</label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Ex: João da Silva">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="document" class="form-label fw-bold text-muted small">Documento (CPF ou RG) *</label>
                                <input type="text" name="document" id="document" class="form-control bg-light border-0 @error('document') is-invalid @enderror" value="{{ old('document') }}" required placeholder="000.000.000-00">
                                @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="birth_date" class="form-label fw-bold text-muted small">Data de Nascimento *</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control bg-light border-0 @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                                @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-bold text-muted small">Gênero Identificado</label>
                                <select name="gender" id="gender" class="form-select bg-light border-0 @error('gender') is-invalid @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Feminino" {{ old('gender') == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                    <option value="Outro" {{ old('gender') == 'Outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold text-muted small">Status Institucional</label>
                                <select name="status" id="status" class="form-select bg-light border-0 @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Matrícula Ativa</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                    <option value="transferred" {{ old('status') == 'transferred' ? 'selected' : '' }}>Transferido</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-geo-alt me-2"></i> Endereço Residencial</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="address_zipcode" class="form-label fw-bold text-muted small">CEP</label>
                                <input type="text" name="address_zipcode" id="address_zipcode" class="form-control bg-light border-0 @error('address_zipcode') is-invalid @enderror" value="{{ old('address_zipcode') }}" placeholder="00000-000">
                            </div>
                            <div class="col-md-8">
                                <label for="address_street" class="form-label fw-bold text-muted small">Logradouro (Rua, Av, etc)</label>
                                <input type="text" name="address_street" id="address_street" class="form-control bg-light border-0 @error('address_street') is-invalid @enderror" value="{{ old('address_street') }}" placeholder="Ex: Rua das Flores">
                            </div>

                            <div class="col-md-3">
                                <label for="address_number" class="form-label fw-bold text-muted small">Número / Apto</label>
                                <input type="text" name="address_number" id="address_number" class="form-control bg-light border-0 @error('address_number') is-invalid @enderror" value="{{ old('address_number') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="address_neighborhood" class="form-label fw-bold text-muted small">Bairro</label>
                                <input type="text" name="address_neighborhood" id="address_neighborhood" class="form-control bg-light border-0 @error('address_neighborhood') is-invalid @enderror" value="{{ old('address_neighborhood') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="address_city" class="form-label fw-bold text-muted small">Cidade</label>
                                <input type="text" name="address_city" id="address_city" class="form-control bg-light border-0 @error('address_city') is-invalid @enderror" value="{{ old('address_city') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="address_state" class="form-label fw-bold text-muted small">UF</label>
                                <input type="text" name="address_state" id="address_state" class="form-control bg-light border-0 @error('address_state') is-invalid @enderror" value="{{ old('address_state') }}" placeholder="SP" maxlength="2">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Coluna Lateral: Contato e Médico -->
                <div class="col-lg-4">
                    
                    <!-- Contato -->
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-telephone me-2"></i> Contato Direto</h5>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold text-muted small">Telefone Celular</label>
                            <input type="text" name="phone" id="phone" class="form-control bg-light border-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="(00) 90000-0000">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold text-muted small">E-mail do Aluno/Responsável</label>
                            <input type="email" name="email" id="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@exemplo.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Ficha Médica -->
                    <div class="glass-card p-4 mb-4 border-danger border-opacity-25">
                        <h5 class="fw-bold mb-4 text-danger"><i class="bi bi-heart-pulse me-2"></i> Ficha Médica</h5>
                        
                        <div class="mb-3">
                            <label for="blood_type" class="form-label fw-bold text-muted small">Tipo Sanguíneo</label>
                            <select name="blood_type" id="blood_type" class="form-select bg-light border-0 @error('blood_type') is-invalid @enderror">
                                <option value="">Não Informado</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                    <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="medical_notes" class="form-label fw-bold text-muted small">Alergias e Condições Especiais</label>
                            <textarea name="medical_notes" id="medical_notes" rows="4" class="form-control bg-light border-0 @error('medical_notes') is-invalid @enderror" placeholder="Descreva alergias a medicamentos, restrições alimentares, síndromes, etc.">{{ old('medical_notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i> Salvar Aluno
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</x-app-layout>
