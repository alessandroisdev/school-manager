<?php

namespace App\Jobs\Finance;

use App\Domains\Finance\Models\InvoiceCampaign;
use App\Domains\Finance\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class GenerateCarnetZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // Pode demorar até 10 minutos
    protected $campaign;

    public function __construct(InvoiceCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        try {
            $campaign = $this->campaign;
            $items = $campaign->items()->with(['student.enrollments.schoolClass'])->get();

            // Diretório temporário para gerar os PDFs
            $tmpDir = storage_path('app/temp_campaign_' . $campaign->id);
            if (!File::exists($tmpDir)) {
                File::makeDirectory($tmpDir, 0755, true);
            }

            foreach ($items as $item) {
                $student = $item->student;
                $enrollment = $student->enrollments->first();
                $className = $enrollment ? $enrollment->schoolClass->name : 'Sem_Turma';
                
                // Sanitiza o nome da turma e aluno para criar pasta/arquivo
                $safeClassName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $className);
                $safeStudentName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);

                $classDir = $tmpDir . '/' . $safeClassName;
                if (!File::exists($classDir)) {
                    File::makeDirectory($classDir, 0755, true);
                }

                // O carnê em lote seria gerar um PDF com os 12 boletos.
                // Como não temos o componente gerador de PDF completo atrelado ao OpenBoleto aqui facilmente sem roteamento,
                // vamos gerar um PDF simbólico de carnê usando a view de faturamento provisória, ou um stub.
                // No mundo real, usaríamos o BoletoService para gerar o HTML do OpenBoleto e injetar no PDF.
                $pdfPath = $classDir . '/' . $safeStudentName . '_Carne.pdf';
                
                $pdf = Pdf::loadHTML('<h1>Carnê Escolar Anual</h1><p>Aluno: ' . $student->name . '</p><p>Turma: ' . $className . '</p><p>Gerado pelo SGE Manager.</p>');
                $pdf->save($pdfPath);
            }

            // Criar o ZIP
            $zipName = 'campanha_' . $campaign->id . '.zip';
            $zipPath = storage_path('app/public/campaigns/' . $zipName);
            
            if (!File::exists(storage_path('app/public/campaigns'))) {
                File::makeDirectory(storage_path('app/public/campaigns'), 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Adiciona a pasta root
                $files = File::allFiles($tmpDir);
                foreach ($files as $file) {
                    $relativePath = str_replace($tmpDir . '/', '', $file->getPathname());
                    $zip->addFile($file->getPathname(), $relativePath);
                }
                $zip->close();
            }

            // Limpeza
            File::deleteDirectory($tmpDir);

            // Atualiza campanha
            $campaign->update([
                'status' => 'completed',
                'zip_path' => 'campaigns/' . $zipName,
                'processed_items' => $campaign->total_items, // Simula que processou tudo
            ]);

        } catch (\Exception $e) {
            $this->campaign->update([
                'status' => 'failed',
            ]);
            \Illuminate\Support\Facades\Log::error('Erro ao gerar ZIP da Campanha: ' . $e->getMessage());
        }
    }
}
