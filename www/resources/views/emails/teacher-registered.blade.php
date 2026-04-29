@extends('emails.layout')

@section('content')
    <h2>Olá, Professor(a) {{ $teacher->name }}!</h2>
    <p>Temos a honra de dar-lhe as boas-vindas ao corpo docente do SGE. Seu cadastro em nossa plataforma acadêmica foi realizado com sucesso.</p>
    
    <p>Abaixo estão as suas credenciais para acesso à <strong>Área do Professor</strong>, onde você poderá gerenciar seus diários de classe, lançar notas, registrar frequências e acompanhar o desempenho de suas turmas:</p>
    
    <div class="info-box">
        <div class="info-row"><span class="info-label">Link de Acesso:</span> <a href="{{ route('login.teacher') }}">{{ route('login.teacher') }}</a></div>
        <div class="info-row"><span class="info-label">E-mail Corporativo:</span> {{ $email }}</div>
        <div class="info-row"><span class="info-label">Senha Inicial:</span> {{ $password }}</div>
    </div>
    
    <p><em>Importante: Recomendamos que você altere sua senha após o primeiro acesso no painel para garantir a segurança da sua conta.</em></p>
    
    <center>
        <a href="{{ route('login.teacher') }}" class="btn">Acessar Diário de Classe</a>
    </center>
    
    <p style="margin-top: 30px;">Desejamos um excelente ano letivo!</p>
    <p>Atenciosamente,<br>Coordenação Pedagógica</p>
@endsection
