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
        if (!Schema::hasColumn('template_mains', 'delete_status')) {
            Schema::table('template_mains', function (Blueprint $table) {
                $table->integer('delete_status')->default(0);
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
        Schema::table('template_mains', function (Blueprint $table) {
            $table->dropColumn('delete_status');
        });
    }
};
