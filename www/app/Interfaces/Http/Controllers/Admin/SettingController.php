<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Shared\Models\UnitSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function index()
    {
        // Pegamos as configurações da unidade ativa na sessão
        $settings = UnitSetting::where('unit_id', session('active_unit_id'))
                        ->pluck('value', 'key')
                        ->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');

        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            UnitSetting::updateOrCreate(
                ['unit_id' => $unitId, 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Configurações da Unidade atualizadas com sucesso!');
    }
}
