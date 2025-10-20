<?php

namespace App\Services;

use App\Models\Cooperative;
use Illuminate\Support\Facades\DB;

class CooperativeProfileService
{
    public function update(Cooperative $cooperative, array $fields): Cooperative
    {
        return DB::transaction(function () use ($cooperative, $fields) {
            $cooperative->update($fields);
            $cooperative->location()->delete();
            $cooperative->location()->updateOrCreate($fields['location'] ?? []);
            $cooperative->phones()->delete();
            $cooperative->phones()->createMany($fields['phones'] ?? []);
            return $cooperative;
        });
    }
}
