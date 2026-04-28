<?php

namespace App\Interfaces\Http\Controllers\Document;

use App\Application\UseCases\Document\GenerateDocumentUseCase;
use App\Domains\Document\Models\Document;
use App\Domains\Document\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentController extends Controller
{
    public function __construct(
        protected GenerateDocumentUseCase $generateDocumentUseCase
    ) {}

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'document_template_id' => 'required|exists:document_templates,id',
            'student_id' => 'required|exists:students,id',
            'extra_data' => 'nullable|array',
        ]);

        try {
            $document = $this->generateDocumentUseCase->execute(
                $validated['unit_id'],
                $validated['document_template_id'],
                $validated['student_id'],
                $validated['extra_data'] ?? []
            );

            return response()->json(['message' => 'Documento gerado com sucesso!', 'document' => $document], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(int $id)
    {
        $document = Document::findOrFail($id);
        // Exibir o HTML purificado (em um cenário real, retornaria uma view imprimível)
        return response($document->generated_content)->header('Content-Type', 'text/html');
    }
}
