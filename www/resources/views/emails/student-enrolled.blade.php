@extends('emails.layout')

@section('content')
    <h2>Olá, {{ $student->name }}!</h2>
    <p>É com grande alegria que confirmamos sua matrícula na nossa instituição. Seja muito bem-vindo(a) à família SGE!</p>
    
    <p>Abaixo estão as suas credenciais para acesso ao <strong>Portal do Aluno</strong>, onde você poderá acompanhar suas notas, frequências, comunicados e muito mais:</p>
    
    <div class="info-box">
        <div class="info-row"><span class="info-label">Link de Acesso:</span> <a href="{{ route('login.student') }}">{{ route('login.student') }}</a></div>
        <div class="info-row"><span class="info-label">Matrícula (Login):</span> {{ $registration }}</div>
        <div class="info-row"><span class="info-label">Senha Inicial:</span> {{ $password }}</div>
    </div>
    
    <p><em>Importante: Recomendamos que você altere sua senha após o primeiro acesso para garantir a segurança da sua conta.</em></p>
    
    <center>
        <a href="{{ route('login.student') }}" class="btn">Acessar Portal do Aluno</a>
    </center>
    
    <p style="margin-top: 30px;">Desejamos um excelente ano letivo!</p>
    <p>Atenciosamente,<br>Equipe Diretiva SGE</p>
@endsection
