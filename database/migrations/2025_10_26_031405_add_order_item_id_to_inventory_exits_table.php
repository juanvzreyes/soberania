<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inventory_exits', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->after('product_id')->constrained('order_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('inventory_exits', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
        });
    }
};
