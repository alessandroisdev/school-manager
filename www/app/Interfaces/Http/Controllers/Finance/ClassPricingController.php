<?php

namespace App\Interfaces\Http\Controllers\Finance;

use App\Domains\Academic\Models\Grade;
use App\Domains\Academic\Models\Shift;
use App\Domains\Finance\Models\ClassPricing;
use App\Http\Controllers\Controller;
use App\Interfaces\Http\Requests\Finance\ClassPricingRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClassPricingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ClassPricing::with(['grade', 'shift'])
                ->where('unit_id', session('active_unit_id'))
                ->latest();
            
            return DataTables::of($query)
                ->addColumn('grade_shift', function($pricing) {
                    return '<div class="fw-bold text-dark mb-0">' . $pricing->grade->name . '</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">' . $pricing->shift->name . '</div>';
                })
                ->addColumn('annual_amount_formatted', function($pricing) {
                    return '<div class="fw-bold text-success">R$ ' . number_format($pricing->annual_amount, 2, ',', '.') . '</div>';
                })
                ->addColumn('installments_info', function($pricing) {
                    return $pricing->installments_count . 'x de R$ ' . number_format($pricing->annual_amount / $pricing->installments_count, 2, ',', '.');
                })
                ->addColumn('actions', function($pricing) {
                    $editUrl = route('finance.pricings.edit', $pricing);
                    $deleteUrl = route('finance.pricings.destroy', $pricing);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    
                    return '
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-pencil"></i></a>
                            <form action="'.$deleteUrl.'" method="POST" class="d-inline-block" onsubmit="return confirm(\'Deseja realmente excluir esta precificação?\');">
                                '.$csrf.$method.'
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['grade_shift', 'annual_amount_formatted', 'actions'])
                ->make(true);
        }

        return view('finance.pricings.index');
    }

    public function create()
    {
        $grades = Grade::all();
        $shifts = Shift::all();
        return view('finance.pricings.create', compact('grades', 'shifts'));
    }

    public function store(ClassPricingRequest $request)
    {
        $data = $request->validated();
        $data['unit_id'] = session('active_unit_id');

        ClassPricing::create($data);

        return redirect()->route('finance.pricings.index')->with('success', 'Precificação cadastrada com sucesso.');
    }

    public function edit(ClassPricing $classPricing)
    {
        if ($classPricing->unit_id != session('active_unit_id')) {
            abort(403);
        }
        
        $grades = Grade::all();
        $shifts = Shift::all();
        
        return view('finance.pricings.edit', compact('classPricing', 'grades', 'shifts'));
    }

    public function update(ClassPricingRequest $request, ClassPricing $classPricing)
    {
        if ($classPricing->unit_id != session('active_unit_id')) {
            abort(403);
        }

        $data = $request->validated();

        $classPricing->update($data);

        return redirect()->route('finance.pricings.index')->with('success', 'Precificação atualizada com sucesso.');
    }

    public function destroy(ClassPricing $classPricing)
    {
        if ($classPricing->unit_id != session('active_unit_id')) {
            abort(403);
        }

        $classPricing->delete();

        return redirect()->route('finance.pricings.index')->with('success', 'Precificação removida com sucesso.');
    }
}

