<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold text-muted">E-mail Corporativo</label>
            <input id="email" class="form-control form-control-lg border-0 shadow-sm" type="email" name="email" value="{{ old('email', 'admin@schoolmanager.com') }}" required autofocus placeholder="nome@escola.com" />
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label fw-bold text-muted">Senha de Acesso</label>
            <input id="password" class="form-control form-control-lg border-0 shadow-sm" type="password" name="password" value="123456789" required autocomplete="current-password" placeholder="••••••••" />
        </div>

        <div class="d-grid gap-2 mt-4">
            <button class="btn btn-primary btn-lg fw-bold rounded-3 shadow-sm" type="submit">
                Acessar o SGE
            </button>
        </div>
    </form>
</x-guest-layout>
