<?php

namespace App\Http\Resources;

use App\Traits\DateFormat; // Asumimos la existencia de este trait
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    use DateFormat;

    /**
     * Transforma el recurso de Categoría en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'is_active'   => (bool) $this->is_active, // Aseguramos que sea un booleano para Vue
            'created_at'  => $this->textFormatDate($this->created_at),
            'updated_at'  => $this->textFormatDate($this->updated_at),
            
            //'products_count' => $this->whenLoaded('products', fn() => $this->products->count()),
        ];
    }
}
