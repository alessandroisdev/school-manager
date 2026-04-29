<?php

namespace App\Jobs\Finance;

use App\Domains\Finance\Models\InvoiceCampaignItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignItem;

    public function __construct(InvoiceCampaignItem $campaignItem)
    {
        $this->campaignItem = $campaignItem;
    }

    public function handle(): void
    {
        try {
            // Em um ambiente real, aqui usaríamos o Mail::to()->send(new InvoiceMail(...))
            // Como este é o MVP e não temos SMTP, vamos apenas simular e fazer um log
            // Se houvesse o SMTP, usaríamos o dompdf ou geraríamos o link do portal
            
            // Simulação de processamento (0.5s)
            usleep(500000); 

            Log::info("Email enviado para {$this->campaignItem->email} referente ao aluno {$this->campaignItem->student->name}");

            $this->campaignItem->update([
                'status' => 'sent',
            ]);

        } catch (\Exception $e) {
            $this->campaignItem->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
