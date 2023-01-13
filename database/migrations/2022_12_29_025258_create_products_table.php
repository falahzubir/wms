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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('code');
            $table->integer('price');
            $table->integer('weight')->default(0);
            $table->integer('is_foc')->default(0);
            $table->unsignedBigInteger('main_product_id')->nullable();
            // $table->foreign('main_product_id')->references('id')->on('products');
            $table->integer('is_active')->default(1)->comment('1-Active, 2-Inactive');
            $table->boolean('sensitive')->default(0)->comment('0-No, 1-Yes');
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
        Schema::dropIfExists('products');
    }
};
