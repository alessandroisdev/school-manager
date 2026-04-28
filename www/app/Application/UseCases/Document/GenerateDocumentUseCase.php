<?php

namespace App\Application\UseCases\Document;

use App\Domains\Document\Models\Document;
use App\Domains\Document\Models\DocumentTemplate;
use App\Domains\Enrollment\Models\Student;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerateDocumentUseCase
{
    public function execute(int $unitId, int $templateId, int $studentId, array $extraData = []): Document
    {
        return DB::transaction(function () use ($unitId, $templateId, $studentId, $extraData) {
            $template = DocumentTemplate::where('unit_id', $unitId)->findOrFail($templateId);
            $student = Student::where('unit_id', $unitId)->with('schoolClasses')->findOrFail($studentId);

            // Preparar variáveis para o motor Blade
            $data = array_merge([
                'student' => $student,
                'schoolClass' => $student->schoolClasses->first(),
                'date' => now()->format('d/m/Y'),
                'time' => now()->format('H:i'),
            ], $extraData);

            // O pulo do gato: Compilar o HTML do banco de dados passando as variáveis
            try {
                $generatedHtml = Blade::render($template->content, $data);
            } catch (\Throwable $e) {
                throw new Exception("Erro ao compilar o template do documento: " . $e->getMessage());
            }

            // Salvar versão imutável
            $document = Document::create([
                'unit_id' => $unitId,
                'student_id' => $studentId,
                'document_template_id' => $template->id,
                'title' => $template->name . ' - ' . $student->name,
                'generated_content' => $generatedHtml,
            ]);

            return $document;
        });
    }
}
