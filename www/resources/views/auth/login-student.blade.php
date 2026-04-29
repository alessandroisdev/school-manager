<x-guest-layout>
    <div class="text-center mb-4 border-bottom pb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-backpack2-fill fs-2"></i>
        </div>
        <h4 class="fw-bold text-body">Portal do Aluno</h4>
        <p class="text-muted small mb-0">Acesse suas notas, presenças e boletos.</p>
    </div>

    <form method="POST" action="{{ route('login.attempt', 'aluno') }}">
        @csrf

        <!-- Login -->
        <div class="mb-3">
            <label for="login" class="form-label fw-bold text-muted">Matrícula</label>
            <input id="login" class="form-control form-control-lg border-0 shadow-sm" type="text" name="login" value="{{ old('login') }}" required autofocus placeholder="Ex: 2026001" />
            @error('login')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-bold text-muted">Senha (Documento)</label>
            <input id="password" class="form-control form-control-lg border-0 shadow-sm" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
        </div>

        <div class="d-grid gap-2 mt-4">
            <button class="btn btn-primary btn-lg fw-bold rounded-3 shadow-sm" type="submit">
                Entrar no Portal
            </button>
        </div>
    </form>
</x-guest-layout>
