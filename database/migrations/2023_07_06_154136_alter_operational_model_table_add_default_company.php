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
        Schema::table('operational_models', function (Blueprint $table) {
            $table->unsignedBigInteger('default_company_id')->nullable()->after('description');
            $table->foreign('default_company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operational_models', function(Blueprint $table) {
            $table->dropForeign('operational_models_default_company_id_foreign');
            $table->dropColumn('default_company_id');
        });
    }
};
