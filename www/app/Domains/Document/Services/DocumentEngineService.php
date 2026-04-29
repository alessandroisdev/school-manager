<?php

namespace App\Domains\Document\Services;

use App\Domains\Document\Models\DocumentTemplate;
use App\Domains\Document\Models\IssuedDocument;
use App\Domains\Enrollment\Models\Student;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentEngineService
{
    /**
     * Generate an IssuedDocument from a Template and a Student.
     */
    public function generateForStudent(DocumentTemplate $template, Student $student, $issuedById = null)
    {
        // 1. Get raw HTML parts
        $headerHtml = $template->header ? $template->header->content : '';
        $footerHtml = $template->footer ? $template->footer->content : '';
        $bodyHtml = $template->content;

        // 2. Parse Variables in Header, Body, and Footer
        $headerHtml = $this->parseVariables($headerHtml, $student);
        $bodyHtml = $this->parseVariables($bodyHtml, $student);
        $footerHtml = $this->parseVariables($footerHtml, $student);

        // 3. Mount Final Frozen HTML
        $frozenHtml = $this->mountFullHtml($headerHtml, $bodyHtml, $footerHtml, $template->watermark_url);

        // 4. Generate Unique Reference Code
        $referenceCode = 'DOC-' . date('Y') . '-' . strtoupper(Str::random(8));

        // 5. Create IssuedDocument
        return IssuedDocument::create([
            'unit_id' => $student->unit_id,
            'student_id' => $student->id,
            'document_template_id' => $template->id,
            'reference_code' => $referenceCode,
            'content' => $frozenHtml,
            'status' => 'valid',
            'issued_by_id' => $issuedById ?? auth()->id(),
        ]);
    }

    /**
     * Replaces shortcodes with actual values.
     */
    protected function parseVariables($html, Student $student)
    {
        if (empty($html)) return '';

        $unit = $student->unit;
        // Pega a matrícula mais recente (apenas para exibição no doc)
        $latestEnrollment = $student->enrollments()->latest()->first();
        $cursoNome = $latestEnrollment && $latestEnrollment->schoolClass 
                     ? $latestEnrollment->schoolClass->name 
                     : 'Não Enturmado';

        $replacements = [
            // Aluno
            '[ALUNO_NOME]' => $student->name,
            '[ALUNO_CPF]' => $student->document,
            '[ALUNO_MATRICULA]' => $student->id . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT), // Exemplo de matrícula
            
            // Responsável (Pode vir de um campo do aluno ou relacionamento se houver)
            '[RESPONSAVEL_NOME]' => 'Responsável Legal do Aluno', 

            // Unidade / Escola
            '[UNIDADE_NOME]' => $unit ? $unit->name : 'Nossa Escola',
            '[UNIDADE_CNPJ]' => $unit ? $unit->document : '00.000.000/0000-00',
            
            // Curso/Turma
            '[CURSO_NOME]' => $cursoNome,
            
            // Sistema
            '[DATA_ATUAL]' => Carbon::now()->isoFormat('D [de] MMMM [de] YYYY'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Builds the final HTML combining all parts and necessary inline CSS.
     */
    protected function mountFullHtml($header, $body, $footer, $watermarkUrl = null)
    {
        $watermarkCss = '';
        if ($watermarkUrl) {
            $watermarkCss = '
                body::before {
                    content: "";
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 80%;
                    height: 80%;
                    background-image: url("' . $watermarkUrl . '");
                    background-repeat: no-repeat;
                    background-position: center;
                    background-size: contain;
                    opacity: 0.1;
                    z-index: -1;
                }
            ';
        }

        $html = '<style>
            body { font-family: sans-serif; font-size: 14px; line-height: 1.5; color: #333; margin: 0; padding: 0; }
            .header-block { margin-bottom: 20px; }
            .body-block { min-height: 500px; text-align: justify; }
            .footer-block { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
            table, th, td { border: 1px solid #dee2e6; }
            th, td { padding: 0.5rem; }
            ' . $watermarkCss . '
        </style>';

        $html .= '<div class="document-wrapper">';
        
        if ($header) {
            $html .= '<div class="header-block">' . $header . '</div>';
        }
        
        $html .= '<div class="body-block">' . $body . '</div>';
        
        if ($footer) {
            $html .= '<div class="footer-block">' . $footer . '</div>';
        }
        
        $html .= '</div>';

        return $html;
    }
}
