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
                'late_fee_interest' => 2.00
            ]
        );

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');

        $validated = $request->validate([
            'settings.passing_grade' => 'required|numeric',
            'settings.passing_attendance' => 'required|numeric',
            'settings.default_class_capacity' => 'required|numeric',
            'settings.current_academic_year' => 'required|numeric',
            'settings.default_due_day' => 'required|numeric',
            'settings.late_fee_interest' => 'required|numeric',
        ]);

        $settings = UnitSetting::where('unit_id', $unitId)->first();
        if ($settings) {
            $settings->update($validated['settings']);
        }

        return redirect()->back()->with('success', 'Configurações da Unidade atualizadas com sucesso!');
    }
}
