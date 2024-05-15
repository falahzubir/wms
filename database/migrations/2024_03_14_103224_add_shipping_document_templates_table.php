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
        //
        Schema::create('shipping_document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('promotional_title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('operational_model_id')->nullable();
            $table->string('platform')->nullable();
            $table->string('link_type')->comment('1:Generate Link QR,2:Upload Photo');
            $table->text('content_path')->nullable();
            $table->text('additional_detail')->nullable()->comment('1:Order ID,2:Tracking Number,3:Phone Number');
            $table->string('promotion_header')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('shipping_document_templates');
    }
};
