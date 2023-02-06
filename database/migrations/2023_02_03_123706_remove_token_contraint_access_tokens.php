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
        Schema::table('access_tokens', function (Blueprint $table) {
            //remove unique constraint
            $table->dropUnique('access_tokens_token_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access_tokens', function (Blueprint $table) {
            //add unique constraint
            $table->unique('token');
        });
    }
};
