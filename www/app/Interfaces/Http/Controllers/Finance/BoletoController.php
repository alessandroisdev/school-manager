<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Finance\Models\Invoice;
use App\Services\Finance\BoletoService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BoletoController extends Controller
{
    public function show(Invoice $invoice)
    {
        $unitId = session('active_unit_id');
        
        // Proteção (Admin ou Dono do boleto)
        // Aqui assumimos admin, depois refinamos o middleware do Parent
        if ($invoice->unit_id != $unitId && !auth()->user()->hasRole('responsavel')) {
            abort(403, 'Acesso não autorizado ao boleto.');
        }

        try {
            $boletoService = new BoletoService();
            $boleto = $boletoService->generateBoleto($invoice);

            // Renderiza o HTML do Boleto nativo do OpenBoleto
            echo $boleto->getOutput();
            exit;

        } catch (\Exception $e) {
            return back()->with('warning', 'Erro ao gerar boleto: ' . $e->getMessage());
        }

    }
}
