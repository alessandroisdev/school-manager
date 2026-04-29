<?php

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domains\OfficialDocument\Models\OfficialDocumentSigner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentSignerController extends Controller
{
    public function index(Request $request)
    {
        $unitId = session('active_unit_id');

        if ($request->ajax()) {
            $query = OfficialDocumentSigner::where('unit_id', $unitId)->latest();
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('info', function($signer) {
                    $status = $signer->is_active 
                        ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Ativo</span>'
                        : '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">Inativo</span>';
                        
                    return '<div class="fw-bold text-dark"><i class="bi bi-person-badge text-primary me-2"></i>' . $signer->name . ' ' . $status . '</div>
                            <div class="small text-muted ms-4">' . $signer->title . '</div>';
                })
                ->addColumn('signature', function($signer) {
                    if ($signer->signature_image_path) {
                        return '<img src="' . Storage::url($signer->signature_image_path) . '" height="40" class="rounded border">';
                    }
                    return '<span class="text-muted small"><i class="bi bi-pen"></i> Física (à caneta)</span>';
                })
                ->addColumn('actions', function($signer) {
                    $editUrl = route('admin.official-signers.edit', $signer);
                    $deleteUrl = route('admin.official-signers.destroy', $signer);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '
                        <div class="text-end text-nowrap">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-light text-primary fw-bold me-2"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline-block form-delete">
                                ' . $csrf . $method . '
                                <button type="submit" class="btn btn-sm btn-light text-danger fw-bold"><i class="bi bi-trash"></i> Excluir</button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['info', 'signature', 'actions'])
                ->make(true);
        }

        return view('admin.official-signers.index');
    }

    public function create()
    {
        return view('admin.official-signers.create');
    }

    public function store(Request $request)
    {
        $unitId = session('active_unit_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'signature_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $validated['unit_id'] = $unitId;
        $validated['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('signature_file')) {
            $path = $request->file('signature_file')->store('uploads/units/' . $unitId . '/signatures', 'public');
            $validated['signature_image_path'] = $path;
        }
        
        OfficialDocumentSigner::create($validated);
        
        return redirect()->route('admin.official-signers.index')->with('success', 'Assinante cadastrado!');
    }

    public function edit(OfficialDocumentSigner $officialSigner)
    {
        if ($officialSigner->unit_id != session('active_unit_id')) abort(403);
        return view('admin.official-signers.edit', compact('officialSigner'));
    }

    public function update(Request $request, OfficialDocumentSigner $officialSigner)
    {
        if ($officialSigner->unit_id != session('active_unit_id')) abort(403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'signature_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('signature_file')) {
            if ($officialSigner->signature_image_path) {
                Storage::disk('public')->delete($officialSigner->signature_image_path);
            }
            $path = $request->file('signature_file')->store('uploads/units/' . $officialSigner->unit_id . '/signatures', 'public');
            $validated['signature_image_path'] = $path;
        } elseif ($request->has('remove_signature')) {
            if ($officialSigner->signature_image_path) {
                Storage::disk('public')->delete($officialSigner->signature_image_path);
            }
            $validated['signature_image_path'] = null;
        }
        
        $officialSigner->update($validated);
        
        return redirect()->route('admin.official-signers.index')->with('success', 'Assinante atualizado!');
    }

    public function destroy(OfficialDocumentSigner $officialSigner)
    {
        if ($officialSigner->unit_id != session('active_unit_id')) abort(403);
        
        if ($officialSigner->signature_image_path) {
            Storage::disk('public')->delete($officialSigner->signature_image_path);
        }
        
        $officialSigner->delete();
        return redirect()->route('admin.official-signers.index')->with('success', 'Assinante excluído!');
    }
}
