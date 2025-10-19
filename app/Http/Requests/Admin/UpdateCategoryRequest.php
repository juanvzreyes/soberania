<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $categoryId = $this->route('category')->id; 
        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El campo "Nombre de la Categoría" es obligatorio.',
            'name.unique' => 'Ya existe una categoría con ese nombre. Por favor, elige otro.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 500 caracteres.',
        ];
    }
}