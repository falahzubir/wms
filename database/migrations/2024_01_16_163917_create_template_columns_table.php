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
        if (!Schema::hasTable('template_columns')) {
            Schema::create('template_columns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('column_position');
                $table->timestamps();
                $table->datetime('deleted_at')->nullable();
                $table->unsignedBigInteger('template_main_id');
                $table->unsignedBigInteger('column_main_id');

                // Foreign key constraints
                $table->foreign('template_main_id')->references('id')->on('template_mains')->onDelete('cascade');
                $table->foreign('column_main_id')->references('id')->on('column_mains')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_columns');
    }
};
