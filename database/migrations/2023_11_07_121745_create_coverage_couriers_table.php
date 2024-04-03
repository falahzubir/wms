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
        Schema::create('coverage_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('postcode');
            $table->tinyInteger('type')->notes('1: cod, 0: non-cod');
            $table->unsignedBigInteger('state_id', false);
            $table->unsignedBigInteger('courier_id', false);
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->tinyInteger('status')->default(IS_ACTIVE)->notes('1: active, 0: inactive');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coverage_couriers');
    }
};
