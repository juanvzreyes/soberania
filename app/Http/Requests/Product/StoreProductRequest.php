<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\PhotoRules;

class StoreProductRequest extends FormRequest
{
    use PhotoRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            $this->photoRules(),
            [
                'name'         => ['required', 'string', 'max:255', 'unique:products,name'],
                'description'  => ['required', 'string', 'max:500'],
                'price'        => ['required', 'numeric', 'min:0'],
                'category_id'  => ['required', 'exists:categories,id'],
                'is_available'    => ['nullable', 'boolean'],
            ]
        );
    }

    public function attributes(): array
    {
        return array_merge(
            $this->photoAttributes(),
            [
                'name'        => 'nombre del producto',
                'description' => 'descripción',
                'price'       => 'costo por unidad',
                'category_id' => 'categoría',
                'is_active'   => 'estado del producto',
            ]
        );
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo ":attribute" es obligatorio.',
            'name.unique'   => 'Ya existe un producto con ese nombre.',
            'name.max'      => 'El nombre no puede exceder los 255 caracteres.',

            'description.max' => 'La descripción no puede exceder los 500 caracteres.',
            'description.required' => 'La descripción del producto es obligatoria',
            'price.required' => 'El campo ":attribute" es obligatorio.',
            'price.numeric'  => 'El campo ":attribute" debe ser un número.',
            'price.min'      => 'El precio no puede ser negativo.',

            'category_id.required' => 'Debe seleccionar una categoría para el producto.',
            'category_id.exists'   => 'La categoría seleccionada no es válida.',

            'photos.array'      => 'Las fotos deben enviarse en un formato válido.',
            'photos.max'        => 'Solo puede subir un máximo de 5 imágenes por producto.',
            'photos.*.image'    => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes'    => 'Las imágenes deben estar en formato JPEG, PNG, JPG o WEBP.',
            'photos.*.max'      => 'Cada imagen no debe superar los 2 MB.',
        ];
    }
}
