<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Siempre debe ser true si la autorización se maneja por middleware (como parece ser tu caso).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
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