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
        Schema::table('order_events', function (Blueprint $table) {
            $table->integer("event_id")->after('id');
            $table->string("event_name")->after('event_id');
            $table->integer('company_id')->foreign('company_id')->references('id')->on('companies')->after('event_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_events', function (Blueprint $table) {
            $table->dropColumn(['event_id','event_name','company_id']);
        });
    }
};
