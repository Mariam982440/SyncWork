<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', Rule::unique('employees', 'email')->ignore($this->employee)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'position'   => ['required', 'string', 'max:100'],
            'hire_date'  => ['required', 'date'],
            'salary'     => ['nullable', 'numeric', 'min:0'],
            'avatar'     => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return (new StoreEmployeeRequest)->messages();
    }
}