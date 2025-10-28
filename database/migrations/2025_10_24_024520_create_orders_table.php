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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cooperative_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'Pendiente',      
                'En preparación',   
                'En camino',     
                'Entregado',    
                'Cancelado'      
            ])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
