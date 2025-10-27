<?php

namespace App\Http\Resources;

use App\Traits\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use DateFormat;
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
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'second_last_name' => $this->second_last_name,
            'gender'        => $this->gender,
            'email'         => $this->email,
            'roles'         => RoleResource::collection($this->whenLoaded('roles')),
            'created_at'    => $this->textFormatDate($this->created_at),
            'deleted_at'    => $this->deleted_at ? $this->textFormatDate($this->deleted_at) : null,
        ];
    }
}
