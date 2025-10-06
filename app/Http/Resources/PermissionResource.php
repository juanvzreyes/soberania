<?php

namespace App\Http\Resources;

use App\Traits\DateFormat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'description'   => $this->description,
            'module_key'    => $this->module_key,
            'created_at'    => $this->textFormatDate($this->created_at),
        ];
    }
}
