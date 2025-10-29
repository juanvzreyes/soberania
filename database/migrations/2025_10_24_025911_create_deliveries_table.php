<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade'); 
            $table->timestamp('estimated_delivery_date')->nullable();
            
            $table->enum('status', [
                'Inicial', 
                'En preparación',     
                'En camino',        
                'Entregado',         
                'Incidencia',           
                'Cancelado',         
            ])->default('Inicial');
            $table->string('signature_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};