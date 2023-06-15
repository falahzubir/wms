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
        Schema::create('third_party_requests', function (Blueprint $table) {
            $table->id();
            $table->string("url");
            $table->string("method");
            $table->json("parameters")->nullable();
            $table->longText("response");
            $table->integer("status_code");
            $table->timestamp("requested_at");
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
        Schema::dropIfExists('third_party_requests');
    }
};
