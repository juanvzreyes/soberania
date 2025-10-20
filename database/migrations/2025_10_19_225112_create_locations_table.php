<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('interior_number')->nullable();
            $table->string('exterior_number')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->morphs('locationable');
            $table->foreignId('neighborhood_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
