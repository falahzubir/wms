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
        Schema::table('shippings', function (Blueprint $table) {
            $table->unsignedBigInteger('scanned_by')->nullable()->after('created_by');
            $table->foreign('scanned_by')->references('id')->on('users');
            $table->timestamp('scanned_at')->nullable()->after('scanned_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('scanned_at');
            $table->dropForeign(['scanned_by']);
            $table->dropColumn('scanned_by');
        });
    }
};
