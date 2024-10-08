<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Change the data type of the 'rate' column to DECIMAL(20, 11)
            $table->decimal('rate', 20, 11)->change();

            // Change the data type of the 'currency' column to BIGINT(20) unsigned and set it as a foreign key
            $table->unsignedBigInteger('currency')->change();
            $table->foreign('currency')->references('id')->on('currencies')->onDelete('cascade');

            // Drop the 'country_id' column
            $table->dropColumn('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Revert the 'rate' column back to its original data type (assumed to be float or double here, adjust if different)
            $table->float('rate')->change();

            // Revert the 'currency' column back to its original data type
            $table->bigInteger('currency')->unsigned(false)->change();
            $table->dropForeign(['currency']); // Drop foreign key constraint

            // Add the 'country_id' column back
            $table->string('country_id');
        });
    }
};
