<x-guest-layout>
    <div class="text-center mb-4 border-bottom pb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h4 class="fw-bold text-body">Acesso Administrativo</h4>
        <p class="text-muted small mb-0">Direção, Secretaria e Gestão Global.</p>
    </div>

    <form method="POST" action="{{ route('login.attempt', 'admin') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="login" class="form-label fw-bold text-muted">E-mail Corporativo</label>
            <input id="login" class="form-control form-control-lg border-0 shadow-sm" type="email" name="login" value="{{ old('login', 'admin@schoolmanager.com') }}" required autofocus placeholder="nome@escola.com" />
            @error('login')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-bold text-muted">Senha de Acesso</label>
            <input id="password" class="form-control form-control-lg border-0 shadow-sm" type="password" name="password" value="123456789" required autocomplete="current-password" placeholder="••••••••" />
        </div>

        <div class="d-grid gap-2 mt-4">
            <button class="btn btn-dark btn-lg fw-bold rounded-3 shadow-sm" type="submit">
                Entrar no ERP
            </button>
        </div>
    </form>
</x-guest-layout>
