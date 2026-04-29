<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\Shared\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContextController extends Controller
{
    /**
     * Alterna a Unidade Ativa na Sessão.
     * Toda query posterior no sistema (graças ao HasUnitScope)
     * ficará isolada nesta nova unidade selecionada.
     */
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        
        // Grava na sessão o ID da unidade atual
        session(['active_unit_id' => $unit->id]);

        return redirect()->back()->with('success', 'Contexto alterado para: ' . $unit->name);
    }
}
