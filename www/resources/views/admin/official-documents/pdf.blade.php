<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $officialDocument->full_number }}</title>
    <style>
        /**
         * Padrão Ofício Presidência da República (ABNT)
         * Papel: A4 (210 x 297 mm)
         * Margem Superior: 30mm
         * Margem Esquerda: 30mm
         * Margem Inferior: 20mm
         * Margem Direita: 15mm
         * Fonte Corpo: Carlito/Calibri/Helvetica 12pt
         * Espaçamento entre linhas: 1.5
         */
        @page {
            margin: 30mm 15mm 20mm 30mm;
        }

        body {
            font-family: "Helvetica", "Arial", sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            text-align: justify;
        }

        /* Marca d'água de Cancelado */
        @if($officialDocument->status === 'cancelled')
        body::before {
            content: "CANCELADO";
            position: fixed;
            top: 40%;
            left: 10%;
            font-size: 80pt;
            color: rgba(255, 0, 0, 0.3);
            transform: rotate(-45deg);
            z-index: -1;
            border: 10px solid rgba(255,0,0,0.3);
            padding: 20px;
        }
        @endif

        /* Cabeçalho com Logo */
        .header {
            text-align: center;
            margin-bottom: 20pt;
        }

        .header img {
            max-height: 80px;
            margin-bottom: 10pt;
        }

        .header h3 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }

        /* Identificação do Expediente */
        .identificacao {
            margin-bottom: 30pt;
            font-weight: bold;
        }

        /* Local e Data */
        .local-data {
            text-align: right;
            margin-bottom: 30pt;
        }

        /* Assunto */
        .assunto {
            margin-bottom: 20pt;
        }

        .assunto span {
            font-weight: bold;
        }

        /* Destinatário */
        .destinatario {
            margin-bottom: 30pt;
        }

        /* Corpo do Texto */
        .conteudo {
            margin-bottom: 40pt;
        }

        /* Recuo de parágrafo (2.5cm segundo manual antigo, hoje é opcional, mas manteremos padrão) */
        .conteudo p {
            text-indent: 2.5cm;
            margin-top: 0;
            margin-bottom: 12pt;
        }

        /* Fecho e Assinatura */
        .assinatura {
            text-align: center;
            margin-top: 60pt;
            page-break-inside: avoid;
        }

        .assinatura img {
            max-height: 60px;
            margin-bottom: 10pt;
        }

        .assinatura .nome {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .assinatura .cargo {
            margin: 0;
        }

        /* Paginação */
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Brasão/Logo">
        @endif
        <h3>{{ $unit->name }}</h3>
    </div>

    <div class="identificacao">
        {{ $officialDocument->category->name }} nº {{ $officialDocument->full_number }}
    </div>

    <div class="local-data">
        {{ $city }}-{{ $state }}, {{ $dateFormatted }}.
    </div>

    @if($officialDocument->recipient)
    <div class="destinatario">
        A Sua Excelência/Senhoria o(a) Senhor(a)<br>
        <strong>{{ $officialDocument->recipient }}</strong><br>
    </div>
    @endif

    <div class="assunto">
        <span>Assunto:</span> {{ $officialDocument->subject }}
    </div>

    <div class="conteudo">
        {!! $officialDocument->content !!}
    </div>

    <div class="assinatura">
        @if($signatureUrl)
            <img src="{{ $signatureUrl }}" alt="Assinatura"><br>
        @else
            <br><br><br>
        @endif
        
        @if($officialDocument->signer_name)
            <p class="nome">{{ $officialDocument->signer_name }}</p>
            <p class="cargo">{{ $officialDocument->signer_title }}</p>
        @endif
    </div>

    <!-- Paginação será tratada pelo dompdf nativamente se precisarmos de scripts, 
         mas pelo manual, ofício não numera página 1, só a partir da 2. 
         Uma abordagem simples em CSS/dompdf é suficiente para o MVP. -->
</body>
</html>
