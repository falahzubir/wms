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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('courier_id')->after('bucket_id')->default(15);
            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->unsignedInteger('customer_type')->after('courier_id')->default(1);
            $table->unsignedInteger('operational_model_id')->after('customer_type')->default(1);
            $table->unsignedInteger('team_id')->after('operational_model_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('team_id');
            $table->dropColumn('operational_model_id');
            $table->dropColumn('customer_type');
            $table->dropForeign('orders_courier_id_foreign');
            $table->dropColumn('courier_id');
        });
    }
};
