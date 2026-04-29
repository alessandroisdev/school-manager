<?php

namespace App\Interfaces\Http\Controllers\Secretariat;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Domains\Document\Models\DocumentTemplate;
use App\Domains\Document\Models\IssuedDocument;
use App\Domains\Enrollment\Models\Student;
use App\Domains\Document\Services\DocumentEngineService;
use Barryvdh\DomPDF\Facade\Pdf;

class IssuedDocumentController extends Controller
{
    protected $engine;

    public function __construct(DocumentEngineService $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Store a newly issued document
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'document_template_id' => 'required|exists:document_templates,id',
        ]);

        $student = Student::where('unit_id', session('active_unit_id'))->findOrFail($request->student_id);
        $template = DocumentTemplate::where('unit_id', session('active_unit_id'))->findOrFail($request->document_template_id);

        $this->engine->generateForStudent($template, $student);

        return back()->with('success', 'Documento emitido e congelado com sucesso!');
    }

    /**
     * Show (Download/Print) the PDF of the issued document
     */
    public function show(IssuedDocument $issuedDocument)
    {
        // Garante isolamento por unidade
        if ($issuedDocument->unit_id != session('active_unit_id')) {
            abort(403, 'Acesso não autorizado a este documento.');
        }

        $html = $issuedDocument->content;

        // Se estiver retificado/cancelado, coloca marca d'água vermelha
        if ($issuedDocument->status !== 'valid') {
            $statusText = strtoupper($issuedDocument->status === 'rectified' ? 'Retificado' : 'Cancelado');
            $watermark = '
                <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); 
                            font-size: 80px; color: rgba(255, 0, 0, 0.2); z-index: 9999; 
                            border: 10px solid rgba(255, 0, 0, 0.2); padding: 20px; font-weight: bold; font-family: sans-serif;">
                    ' . $statusText . '
                </div>';
            $html = str_replace('<div class="document-wrapper">', '<div class="document-wrapper">' . $watermark, $html);
        }

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->stream($issuedDocument->reference_code . '.pdf');
    }

    /**
     * Cancel an issued document
     */
    public function cancel(IssuedDocument $issuedDocument)
    {
        if ($issuedDocument->unit_id != session('active_unit_id')) abort(403);
        
        $issuedDocument->update(['status' => 'cancelled']);
        return back()->with('success', 'Documento cancelado com sucesso.');
    }

    /**
     * Rectify a document (invalidate old, generate new)
     */
    public function rectify(Request $request, IssuedDocument $issuedDocument)
    {
        if ($issuedDocument->unit_id != session('active_unit_id')) abort(403);
        
        $student = $issuedDocument->student;
        $template = $issuedDocument->template;

        if (!$template) {
            return back()->with('error', 'O template original foi excluído. Não é possível retificar automaticamente.');
        }

        // Gera novo
        $newDoc = $this->engine->generateForStudent($template, $student);

        // Invalida antigo
        $issuedDocument->update([
            'status' => 'rectified',
            'rectified_by_id' => $newDoc->id
        ]);

        return back()->with('success', 'Documento retificado! O documento antigo foi marcado como inválido.');
    }
}
