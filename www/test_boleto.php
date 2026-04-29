<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $invoice = \App\Domains\Finance\Models\Invoice::find(2365);
    if (!$invoice) {
        echo "Fatura 2365 não encontrada.\n";
        exit;
    }
    
    $boletoService = app(\App\Services\Finance\BoletoService::class);
    $boleto = $boletoService->generateBoleto($invoice);
    echo "OK - Boleto gerado com sucesso.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
