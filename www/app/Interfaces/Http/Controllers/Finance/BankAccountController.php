<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Finance\Models\BankAccount;
use App\Http\Controllers\Controller;
use App\Interfaces\Http\Requests\Finance\BankAccountRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankAccount::where('unit_id', session('active_unit_id'))->latest();
            
            return DataTables::of($query)
                ->addColumn('status_badge', function($account) {
                    return $account->is_active 
                        ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Ativo</span>'
                        : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Inativo</span>';
                })
                ->addColumn('actions', function($account) {
                    $editUrl = route('finance.accounts.edit', $account);
                    $deleteUrl = route('finance.accounts.destroy', $account);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    
                    return '
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-pencil"></i></a>
                            <form action="'.$deleteUrl.'" method="POST" class="d-inline-block" onsubmit="return confirm(\'Deseja realmente excluir esta conta bancária?\');">
                                '.$csrf.$method.'
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('finance.accounts.index');
    }

    public function create()
    {
        return view('finance.accounts.create');
    }

    public function store(BankAccountRequest $request)
    {
        $data = $request->validated();
        $data['unit_id'] = session('active_unit_id');
        $data['is_active'] = $request->has('is_active');

        BankAccount::create($data);

        return redirect()->route('finance.accounts.index')->with('success', 'Conta bancária cadastrada com sucesso.');
    }

    public function edit(BankAccount $bankAccount)
    {
        if ($bankAccount->unit_id != session('active_unit_id')) {
            abort(403);
        }
        
        return view('finance.accounts.edit', compact('bankAccount'));
    }

    public function update(BankAccountRequest $request, BankAccount $bankAccount)
    {
        if ($bankAccount->unit_id != session('active_unit_id')) {
            abort(403);
        }

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $bankAccount->update($data);

        return redirect()->route('finance.accounts.index')->with('success', 'Conta bancária atualizada com sucesso.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->unit_id != session('active_unit_id')) {
            abort(403);
        }

        $bankAccount->delete();

        return redirect()->route('finance.accounts.index')->with('success', 'Conta bancária removida com sucesso.');
    }
}

