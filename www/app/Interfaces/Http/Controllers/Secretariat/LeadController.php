<?php

namespace App\Interfaces\Http\Controllers\Secretariat;

use App\Domains\Enrollment\Models\PreEnrollment;
use App\Domains\Enrollment\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PreEnrollment::with('grade')->where('status', 'pending')->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('lead_info', function($lead) {
                    return '<div class="fw-bold text-dark mb-0">' . $lead->student_name . '</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">Interesse: ' . ($lead->grade->name ?? 'Não Informado') . '</div>';
                })
                ->addColumn('parent_info', function($lead) {
                    $phone = '<i class="bi bi-whatsapp text-success me-1"></i> ' . $lead->phone;
                    $email = $lead->email ? '<br><small class="text-muted"><i class="bi bi-envelope"></i> ' . $lead->email . '</small>' : '';
                    return '<div class="fw-bold">' . $lead->parent_name . '</div>' . $phone . $email;
                })
                ->addColumn('actions', function($lead) {
                    $approveUrl = route('secretariat.leads.approve', $lead);
                    $rejectUrl = route('secretariat.leads.reject', $lead);
                    $csrf = csrf_field();
                    $method = method_field('POST');

                    return '
                        <div class="text-end text-nowrap">
                            <form action="' . $approveUrl . '" method="POST" class="d-inline-block">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm me-2"><i class="bi bi-check-lg"></i> Aprovar (Matricular)</button>
                            </form>
                            <form action="' . $rejectUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-x-lg"></i> Rejeitar</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['lead_info', 'parent_info', 'actions'])
                ->make(true);
        }

        return view('secretariat.leads.index');
    }

    public function approve(PreEnrollment $lead)
    {
        DB::transaction(function() use ($lead) {
            // Cria o Aluno Real no BD
            Student::create([
                'unit_id' => $lead->unit_id,
                'name' => $lead->student_name,
                'birth_date' => now()->subYears(10), // Mock data de nascimento para passar a validacao
                // Observações com os dados dos pais
                'medical_notes' => "Responsável: " . $lead->parent_name . " | Tel: " . $lead->phone . " | Email: " . $lead->email,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'status' => 'active'
            ]);

            // Atualiza status do Lead
            $lead->update(['status' => 'approved']);
        });

        return redirect()->route('secretariat.leads.index')->with('success', 'Lead convertido em Aluno com sucesso!');
    }

    public function reject(PreEnrollment $lead)
    {
        $lead->update(['status' => 'rejected']);
        return redirect()->route('secretariat.leads.index')->with('success', 'Lead arquivado.');
    }
}
