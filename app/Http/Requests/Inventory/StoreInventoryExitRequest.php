<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required', 
                'integer', 
                'exists:products,id',
            ],
            // Usaremos 'quantity' como el campo de salida, que debe ser la cantidad a restar
            'quantity' => ['required', 'integer', 'min:1'], 
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'product_id.required' => 'Debe seleccionar un producto.',
            'quantity.required' => 'La cantidad de salida es obligatoria.',
            'quantity.min' => 'La cantidad mínima de salida es 1.',
            'reason.required' => 'Debe especificar una razón para la salida.',
        ];
    }
}