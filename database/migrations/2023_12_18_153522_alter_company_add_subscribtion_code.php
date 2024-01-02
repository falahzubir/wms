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
        Schema::table('companies', function ($table) {
            if (!Schema::hasColumn('companies', 'posmalaysia_subscribtion_code')) {
                $table->string('posmalaysia_subscribtion_code')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', static function (Blueprint $table) {
            $table->dropColumn('posmalaysia_subscribtion_code');
        });
    }
};
