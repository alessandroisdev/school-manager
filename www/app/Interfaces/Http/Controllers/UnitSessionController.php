<?php

namespace App\Interfaces\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UnitSessionController extends Controller
{
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
        ]);

        $user = Auth::user();
        if ($user && $user->units->contains($validated['unit_id'])) {
            session(['active_unit_id' => $validated['unit_id']]);
            return back()->with('status', 'Unidade alterada com sucesso.');
        }

        return back()->withErrors(['unit_id' => 'Você não tem acesso a esta unidade.']);
    }
}
