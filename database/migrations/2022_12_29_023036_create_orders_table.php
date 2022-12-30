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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_id');
            $table->integer('company_id')->comment('1-EMZI Holding, 2-EMZI Digital');
            $table->text('customer_data');
            $table->integer('order_price');
            $table->integer('shipping_price');
            $table->integer('total_price');
            $table->unsignedInteger('sold_by');
            $table->unsignedInteger('event_id')->nullable();
            $table->unsignedInteger('bucket_id')->nullable();
            $table->foreign('bucket_id')->references('id')->on('buckets');
            $table->unsignedInteger('courier_type')->nullable();
            $table->string('shipping_number')->nullable();
            $table->string('shipping_remarks')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
