<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function index()
    {
        // Pega as configuracoes da unidade (ou cria padrao)
        $settings = UnitSetting::firstOrCreate(
            ['unit_id' => session('active_unit_id')],
            [
                'calculation_rule' => 'simple',
                'passing_grade' => 6.00,
                'passing_attendance' => 75.00,
                'default_class_capacity' => 30,
                'current_academic_year' => date('Y'),
                'default_due_day' => 10,
                'late_fee_interest' => 2.00,
                'evaluation_type' => 'bimonthly',
                'attendance_type' => 'daily',
                'late_fee_penalty' => 2.00,
                'discount_before_due' => 0.00,
                'currency' => 'BRL',
                'primary_color' => '#0d6efd',
                'timezone' => 'America/Sao_Paulo',
                'enable_student_portal' => true,
                'enable_teacher_portal' => true,
            ]
        );

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');

        $validated = $request->validate([
            'settings.calculation_rule' => 'required|in:simple,weighted',
            'settings.passing_grade' => 'required|numeric',
            'settings.passing_attendance' => 'required|numeric',
            'settings.default_class_capacity' => 'required|numeric',
            'settings.current_academic_year' => 'required|numeric',
            'settings.default_due_day' => 'required|numeric',
            'settings.late_fee_interest' => 'required|numeric',
            'settings.evaluation_type' => 'required|in:bimonthly,trimester,semester',
            'settings.attendance_type' => 'required|in:daily,per_lesson',
            'settings.late_fee_penalty' => 'required|numeric',
            'settings.discount_before_due' => 'required|numeric',
            'settings.currency' => 'required|string|max:3',
            'settings.primary_color' => 'required|string|max:7',
            'settings.receipt_header' => 'nullable|string',
            'settings.receipt_footer' => 'nullable|string',
            'settings.timezone' => 'required|string',
        ]);

        $settingsData = $validated['settings'];
        $settingsData['enable_student_portal'] = $request->has('settings.enable_student_portal');
        $settingsData['enable_teacher_portal'] = $request->has('settings.enable_teacher_portal');

        $settings = UnitSetting::where('unit_id', $unitId)->first();
        if ($settings) {
            $settings->update($settingsData);
        }

        return redirect()->back()->with('success', 'Configurações da Unidade atualizadas com sucesso!');
    }
}
