<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('picking_sequences', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('product_id'); 
            $table->integer('sequence');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picking_sequences');
    }
};
