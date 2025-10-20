<?php

namespace App\Services;

use App\Models\Producer;
use Illuminate\Support\Facades\DB;

class ProducerProfileService
{
    public function update(Producer $producer, array $fields): Producer
    {
        return DB::transaction(function () use ($producer, $fields) {
            $producer->update($fields);
            $producer->location()->delete();
            $producer->location()->updateOrCreate($fields['location'] ?? []);
            $producer->phones()->delete();
            $producer->phones()->createMany($fields['phones'] ?? []);
            return $producer;
        });
    }
}
