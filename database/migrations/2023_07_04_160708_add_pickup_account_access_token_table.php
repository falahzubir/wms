<?php

use App\Models\AccessToken;
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
            $table->json('additional_data')->nullable()->after('token');
        });

        foreach (DHL_SOLD_PICKUP_ACCT as $key => $value) {
            AccessToken::where('company_id', $key)->update([
                'additional_data' => json_encode([
                    'dhl_pickup_account' => $value,
                    'dhl_sold_to_account' => $value
                ])
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access_tokens', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
    }
};
