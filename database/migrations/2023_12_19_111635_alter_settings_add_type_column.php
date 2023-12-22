<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\ScanSettingSeeder;
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
        Schema::table('settings', function ($table) {
            if (!Schema::hasColumn('settings', 'type')) {
                $table->integer('type')->default(1)->after('parent_id')->comment('1-general, 2-scan');
                $table->string('data_type')->default('string')->after('type')->comment('data type of setting');
                $table->text('description')->nullable()->after('data_type')->comment('description of setting');
            }
        });

        //run seeder
        $seeder = new ScanSettingSeeder();
        $seeder->run();

        $permission = new RolesAndPermissionsSeeder();
        $permission->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        //run seeder
        $seeder = new ScanSettingSeeder();
        $seeder->down();

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('data_type');
            $table->dropColumn('type');
        });
    }
};
