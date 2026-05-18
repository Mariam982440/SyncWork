<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:employees,email'],
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
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => "L'adresse e-mail est obligatoire.",
            'email.unique'        => 'Cette adresse e-mail est déjà utilisée.',
            'department.required' => 'Le département est obligatoire.',
            'position.required'   => 'Le poste est obligatoire.',
            'hire_date.required'  => "La date d'embauche est obligatoire.",
            'hire_date.date'      => "La date d'embauche est invalide.",
            'avatar.image'        => 'Le fichier doit être une image.',
            'avatar.max'          => "L'image ne doit pas dépasser 2 Mo.",
        ];
    }
}