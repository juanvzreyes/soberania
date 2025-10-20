<?php

namespace App\Http\Requests\Traits;

trait LocationRules
{
    protected function locationRules(): array
    {
        return [
            'location.state_id'         => ['nullable', 'exists:states,id'],
            'location.municipality_id'  => ['nullable', 'exists:municipalities,id'],
            'location.neighborhood_id'  => ['nullable', 'exists:neighborhoods,id'],
            'location.postal_code'      => ['nullable', 'string', 'digits:5'],
            'location.street'           => ['nullable', 'string', 'max:255'],
            'location.exterior_number'  => ['nullable', 'string', 'max:50'],
            'location.interior_number'  => ['nullable', 'string', 'max:50'],
            'location.latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'location.longitude'        => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
