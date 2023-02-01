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
        Schema::create('bucket_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('bucket_id');
            $table->foreign('bucket_id')->references('id')->on('buckets');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table)
        {
           $table->unsignedBigInteger('bucket_batch_id')->after('bucket_id')->nullable();
           $table->foreign('bucket_batch_id')->references('id')->on('bucket_batches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->dropForeign('orders_bucket_batch_id_foreign');
            $table->dropColumn('bucket_batch_id');
        });

        Schema::dropIfExists('bucket_batches');
    }
};
