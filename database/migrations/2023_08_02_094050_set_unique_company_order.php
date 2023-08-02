<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', fn(Blueprint $table) => $table->unique(['company_id', 'sales_id'], 'company_sales_unique'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', fn(Blueprint $table) => $table->dropUnique('company_sales_unique'));
    }
};
