<?php

namespace App\Services;

use App\Models\Consumer;
use Illuminate\Support\Facades\DB;

class ConsumerProfileService
{
    public function update(Consumer $consumer, array $fields): Consumer
    {
        return DB::transaction(function () use ($consumer, $fields) {
            $consumer->update($fields);
            $consumer->location()->updateOrCreate([], $fields['location'] ?? []);
            $consumer->phones()->delete();
            $consumer->phones()->createMany($fields['phones'] ?? []);
            return $consumer;
        });
    }
}
