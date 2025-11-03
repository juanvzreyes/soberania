<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\PhotoRules;

class UpdateProductRequest extends FormRequest
{
    use PhotoRules;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isQuickToggle = array_keys($this->except('_method', '_token')) === ['is_available'];

        if ($isQuickToggle) {
            return [
                'is_available' => ['required', 'boolean'],
            ];
        }
        $productId = $this->route('product')->id;

        return array_merge(
            $this->photoRules(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'name')->ignore($productId),
                ],
                'description' => ['required', 'string', 'max:1000'],
                'price' => ['required', 'numeric', 'min:0'],
                'category_id' => ['nullable', 'exists:categories,id'],

            ]
        );
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo "Nombre del Producto" es obligatorio.',
            'name.unique' => 'Ya existe un producto con ese nombre. Por favor, elige otro.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'price.required' => 'El campo "Precio" es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio no puede ser negativo.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}
