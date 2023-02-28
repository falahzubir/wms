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
            $table->boolean('is_send')->default(false)->after('attachment');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('url')->nullable()->after('name');
        });

        // set url for companies
        \App\Models\Company::where('id', 1)->update(['url' => 'https://bosemzi.com']);
        \App\Models\Company::where('id', 2)->update(['url' => 'https://aa.bosemzi.com']);
        \App\Models\Company::where('id', 3)->update(['url' => 'https://qastg.bosemzi.com']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('url');
        });
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('is_send');
        });
    }
};
