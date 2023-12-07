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
        Schema::create('alternative_postcode', function (Blueprint $table) {
            $table->id();
            $table->string('state');
            $table->string('actual_postcode');
            $table->string('actual_city');
            $table->string('alternative_postcode');
            $table->string('alternative_city');
            $table->timestamps();
            $table->unsignedBigInteger('delete_status', 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alternative_postcode');
    }
};
