@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-mortarboard-fill fs-2"></i>
                </div>
                <h4 class="fw-bold">Acesso ao SGE</h4>
                <p class="text-muted small">Informe suas credenciais para continuar.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
                    <label for="email">E-mail corporativo</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                    <label for="password">Senha de acesso</label>
                </div>
                
                <button class="btn btn-primary w-100 py-2 fw-bold" type="submit">
                    Entrar <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
