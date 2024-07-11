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
        Schema::table('shippings', function (Blueprint $table) {
            $table->integer('total_weight')->after('receiver_phone_2')->default(0)->comment('in gram');
            $table->foreignId('shipping_cost_id')->after('total_weight')->nullable()->constrained('shipping_costs')->comment('id of shipping cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('total_weight');
            $table->dropForeign(['shipping_cost_id']);
            $table->dropColumn('shipping_cost_id');
        });
    }
};
