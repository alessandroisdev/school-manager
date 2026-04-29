<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Documento</title>
    <style>
        @page {
            margin: 100px 50px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        #header {
            position: fixed;
            left: 0px;
            top: -80px;
            right: 0px;
            height: 80px;
            text-align: center;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: -80px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        /* Classes utilitárias que o TinyMCE pode gerar */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        
        /* Quebra de página manual do TinyMCE */
        .mce-pagebreak {
            page-break-after: always;
            border: none;
        }
    </style>
</head>
<body>
    @if($header)
        <div id="header">
            {!! $header !!}
        </div>
    @endif

    @if($footer)
        <div id="footer">
            {!! $footer !!}
            <div style="margin-top: 10px;">Página <span class="pagenum"></span></div>
        </div>
    @endif

    <div id="content">
        {!! $content !!}
    </div>
</body>
</html>
