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
        Schema::create('bucket_automations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bucket_id');
            $table->foreign('bucket_id')->references('id')->on('buckets');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('operational_model_id')->nullable();
            $table->foreign('operational_model_id')->references('id')->on('operational_models');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')->references('id')->on('order_events');
            $table->integer('shipment_type')->nullable();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->unsignedBigInteger('payment_type_id')->nullable()->comment('23-Shopee, 24-Tiktok');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('priority')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('bucket_automations');
    }
};
