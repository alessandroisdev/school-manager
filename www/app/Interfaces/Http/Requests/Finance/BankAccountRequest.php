<?php

namespace App\Interfaces\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin') || $this->user()->hasRole('diretor');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10',
            'agency' => 'required|string|max:20',
            'account' => 'required|string|max:20',
            'wallet' => 'nullable|string|max:20',
            'fine_percentage' => 'required|numeric|min:0|max:100',
            'interest_percentage' => 'required|numeric|min:0|max:100',
            'instruction_lines' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
