<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Editar Unidade</h2>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('admin.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-building me-2"></i> Dados da Unidade Escolar</h5>
                
                <div class="row g-4">
                    <div class="col-md-12">
                        <label for="school_id" class="form-label fw-bold text-muted small">Rede Escolar (Matriz) *</label>
                        <select name="school_id" id="school_id" class="form-select bg-light border-0" required>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $unit->school_id == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="name" class="form-label fw-bold text-muted small">Nome da Unidade/Filial *</label>
                        <input type="text" name="name" id="name" class="form-control bg-light border-0" value="{{ $unit->name }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="document" class="form-label fw-bold text-muted small">CNPJ / Documento</label>
                        <input type="text" name="document" id="document" class="form-control bg-light border-0" value="{{ $unit->document }}">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold text-muted small">E-mail Institucional</label>
                        <input type="email" name="email" id="email" class="form-control bg-light border-0" value="{{ $unit->email }}">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-bold text-muted small">Telefone Principal</label>
                        <input type="text" name="phone" id="phone" class="form-control bg-light border-0" value="{{ old('phone', $unit->phone) }}">
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-bold text-muted small">Endereço Completo</label>
                        <input type="text" name="address" id="address" class="form-control bg-light border-0" value="{{ old('address', $unit->address) }}" placeholder="Ex: Rua das Flores, 123">
                    </div>

                    <div class="col-md-6">
                        <label for="city" class="form-label fw-bold text-muted small">Cidade</label>
                        <input type="text" name="city" id="city" class="form-control bg-light border-0" value="{{ old('city', $unit->city) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="state" class="form-label fw-bold text-muted small">Estado (UF)</label>
                        <input type="text" name="state" id="state" class="form-control bg-light border-0" value="{{ old('state', $unit->state) }}" placeholder="Ex: SP" maxlength="2">
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" {{ $unit->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Unidade Operacional (Ativa)</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-check-lg me-2"></i> Atualizar Unidade
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
