<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProducerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'certification' => $this->certification,
            'user_id'       => $this->user_id,
            'location'      => new LocationResource($this->whenLoaded('location')),
            'phones'        => $this->whenLoaded('phones'),
        ];
    }
}
