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
        Schema::table('shippings', function (Blueprint $table) {
            $table->string('courier')->nullable()->change();
            $table->string('shipment_number')->nullable()->change();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone_1')->nullable();
            $table->string('receiver_phone_2')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('receiver_phone_2');
            $table->dropColumn('receiver_phone_1');
            $table->dropColumn('receiver_name');
            $table->string('shipment_number')->nullable(false)->change();
            $table->string('courier')->nullable(false)->change();
        });
    }
};
