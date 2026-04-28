@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Painel de Controle</h2>
            <p class="text-muted">Bem-vindo(a), {{ Auth::user()->name }}! Você está acessando o SGE.</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-buildings fs-1"></i>
                    </div>
                    <h5>Unidade Atual</h5>
                    <p class="text-muted mb-0">Unidade ID: {{ Session::get('current_unit_id') ?? 'Nenhuma' }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-shield-lock fs-1"></i>
                    </div>
                    <h5>Seus Papéis</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-1 mt-2">
                        @foreach(Auth::user()->roles as $role)
                            <span class="badge bg-secondary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
