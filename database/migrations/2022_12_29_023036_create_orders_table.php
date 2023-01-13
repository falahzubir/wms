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
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->integer('total_price');
            $table->unsignedInteger('purchase_type')->comment('1-COD, 2-Paid, 3-Installment');
            $table->unsignedInteger('sold_by');
            $table->unsignedInteger('event_id')->nullable();
            $table->unsignedBigInteger('bucket_id')->nullable();
            $table->foreign('bucket_id')->references('id')->on('buckets');
            $table->unsignedInteger('courier_type')->nullable();
            $table->string('shipping_number')->nullable();
            $table->string('shipping_remarks')->nullable();
            $table->unsignedInteger('status')->default(1);
            $table->boolean('is_active')->default(1);
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
