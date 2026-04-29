<?php

namespace App\Interfaces\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin') || $this->user()->hasRole('diretor');
    }

    public function rules(): array
    {
        $pricingId = $this->route('class_pricing') ? $this->route('class_pricing')->id : null;

        return [
            'grade_id' => [
                'required',
                'exists:grades,id',
                Rule::unique('class_pricings', 'grade_id')->where(function ($query) {
                    return $query->where('shift_id', $this->shift_id)
                                 ->where('unit_id', session('active_unit_id'));
                })->ignore($pricingId)
            ],
            'shift_id' => 'required|exists:shifts,id',
            'annual_amount' => 'required|numeric|min:0',
            'installments_count' => 'required|integer|min:1|max:24',
            'default_due_day' => 'required|integer|min:1|max:31',
        ];
    }

    public function messages(): array
    {
        return [
            'grade_id.unique' => 'Já existe uma precificação cadastrada para esta Série neste Turno.',
        ];
    }
}
