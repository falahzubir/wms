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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('sales_type')->nullable()->comment('1:From Campaign; 2:Repeat order; 3:Direct order; 4:Clone order; 5:Affiliate; 6:Gift order; 7:Event order; 8:Repost order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sales_type');
        });
    }
};
