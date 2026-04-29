<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Document\Models\DocumentPartial;
use App\Domains\Document\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');
        
        if ($request->ajax()) {
            $query = DocumentTemplate::with(['header', 'footer'])->where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->editColumn('type', function($template) {
                    $badges = [
                        'contract' => '<span class="badge bg-primary">Contrato</span>',
                        'certificate' => '<span class="badge bg-success">Certificado</span>',
                        'receipt' => '<span class="badge bg-info text-dark">Recibo</span>',
                        'statement' => '<span class="badge bg-warning text-dark">Declaração</span>',
                        'other' => '<span class="badge bg-secondary">Outro</span>',
                    ];
                    return $badges[$template->type] ?? $badges['other'];
                })
                ->addColumn('partials', function($template) {
                    $h = $template->header ? '<span class="badge bg-light text-dark border me-1"><i class="bi bi-layout-text-window-reverse"></i> ' . $template->header->name . '</span>' : '';
                    $f = $template->footer ? '<span class="badge bg-light text-dark border"><i class="bi bi-layout-text-window"></i> ' . $template->footer->name . '</span>' : '';
                    return $h . $f;
                })
                ->addColumn('actions', function($template) {
                    $editUrl = route('admin.templates.edit', $template);
                    $previewUrl = route('admin.templates.preview', $template);
                    $deleteUrl = route('admin.templates.destroy', $template);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $previewUrl . '" target="_blank" class="btn btn-sm btn-light text-info fw-bold me-2"><i class="bi bi-file-earmark-pdf"></i> Preview PDF</a>
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-magic"></i> Builder</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['type', 'partials', 'actions'])
                ->make(true);
        }

        return view('admin.templates.index');
    }

    public function create()
    {
        $unitId = session('active_unit_id');
        $headers = DocumentPartial::where('unit_id', $unitId)->where('type', 'header')->where('is_active', true)->get();
        $footers = DocumentPartial::where('unit_id', $unitId)->where('type', 'footer')->where('is_active', true)->get();
        
        return view('admin.templates.builder', compact('headers', 'footers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:contract,certificate,receipt,statement,other',
            'header_id' => 'nullable|exists:document_partials,id',
            'footer_id' => 'nullable|exists:document_partials,id',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['unit_id'] = session('active_unit_id');
        $validated['is_active'] = $request->has('is_active');

        // Em uma implementação avançada, aqui trataríamos o upload do watermark_path

        DocumentTemplate::create($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Template salvo com sucesso!');
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        $unitId = session('active_unit_id');
        $headers = DocumentPartial::where('unit_id', $unitId)->where('type', 'header')->where('is_active', true)->get();
        $footers = DocumentPartial::where('unit_id', $unitId)->where('type', 'footer')->where('is_active', true)->get();
        
        return view('admin.templates.builder', compact('documentTemplate', 'headers', 'footers'));
    }

    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:contract,certificate,receipt,statement,other',
            'header_id' => 'nullable|exists:document_partials,id',
            'footer_id' => 'nullable|exists:document_partials,id',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $documentTemplate->update($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Template atualizado com sucesso!');
    }

    public function destroy(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->delete();
        return redirect()->route('admin.templates.index')->with('success', 'Template removido!');
    }

    public function previewPdf(DocumentTemplate $documentTemplate)
    {
        $html = $documentTemplate->content;
        
        // Mock data para as variáveis mágicas
        $mockData = [
            '[ALUNO_NOME]' => 'João da Silva Sauro',
            '[ALUNO_CPF]' => '123.456.789-00',
            '[ALUNO_MATRICULA]' => 'MAT2026001',
            '[RESPONSAVEL_NOME]' => 'Maria José da Silva',
            '[UNIDADE_NOME]' => 'Escola Modelo - Unidade Centro',
            '[UNIDADE_CNPJ]' => '12.345.678/0001-90',
            '[DATA_ATUAL]' => date('d/m/Y'),
            '[CURSO_NOME]' => 'Ensino Médio - 1º Ano',
        ];

        // Replace shortcodes
        $html = str_replace(array_keys($mockData), array_values($mockData), $html);

        // Render PDF
        $pdf = Pdf::loadView('admin.templates.pdf_layout', [
            'header' => $documentTemplate->header ? $documentTemplate->header->content : '',
            'footer' => $documentTemplate->footer ? $documentTemplate->footer->content : '',
            'content' => $html,
            'watermark' => $documentTemplate->watermark_path // Caso haja
        ]);

        return $pdf->stream($documentTemplate->name . ' - Preview.pdf');
    }
}
