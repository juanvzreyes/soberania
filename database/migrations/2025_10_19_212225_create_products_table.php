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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            //$table->string('unit_of_measure', 50)->nullable(); 
            $table->integer('stock_quantity')->default(0); 
            $table->boolean('is_available')->default(true); 
            $table->timestamps();
            $table->softDeletes(); 
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};