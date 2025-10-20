<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CooperativeResource extends JsonResource
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
            'name'          => $this->name,
            'region'        => $this->region,
            'members'       => $this->members,
            'user_id'       => $this->user_id,
            'location'      => new LocationResource($this->whenLoaded('location')),
            'phones'        => $this->whenLoaded('phones'),
        ];
    }
}
