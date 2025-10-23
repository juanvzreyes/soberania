<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryEntryRequest extends FormRequest
{
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
            // 1. product_id: Obligatorio y debe existir en la tabla 'products'
            'product_id' => [
                'required', 
                'integer', 
                Rule::exists('products', 'id')->where(function ($query) {
                    // Opcional: asegurar que el producto pertenezca al productor logueado
                    // $query->where('producer_id', auth()->id()); 
                }),
            ],
            
            // 2. quantity: Obligatorio, entero, y debe ser al menos 1
            'quantity' => ['required', 'integer', 'min:1'],
            
            // 3. reason: Obligatorio para propósitos de auditoría
            'reason' => ['required', 'string', 'max:500'],
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
            'product_id.required' => 'Debe seleccionar un producto para registrar la entrada.',
            'product_id.exists' => 'El producto seleccionado no es válido.',
            'quantity.required' => 'El campo "Cantidad de Entrada" es obligatorio.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad mínima de entrada es 1.',
            'reason.required' => 'Debe especificar una razón para la entrada de inventario.',
            'reason.max' => 'La razón no puede exceder los 500 caracteres.',
        ];
    }
}
