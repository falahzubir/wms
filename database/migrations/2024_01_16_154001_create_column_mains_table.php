<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('column_mains')) {
            Schema::create('column_mains', function (Blueprint $table) {
                $table->id();
                $table->string('column_name');
                $table->string('column_display_name')->nullable();
                $table->timestamps();
                $table->dateTimeTz('deleted_at')->nullable();
            });

            // Add the following lines to insert data into the column_name column
            $values = [
                'blank', 'sales_id', 'companies_name', 'companies_phone', 'companies_address', 'companies_postcode',
                'companies_city', 'companies_state', 'companies_country', 'customers_name', 'customers_phone',
                'customers_phone_2', 'customers_address', 'customers_postcode', 'customers_city', 'customers_state',
                'customers_country', 'purchase_type', 'payment_type_name', 'operational_models_name', 'couriers_name',
                'shipping_number', 'shipping_remarks', 'total_price', 'sales_remark', 'payment_refund',
                'quantity', 'weight', 'item_description'
            ];

            foreach ($values as $value) {
                DB::table('column_mains')->insert(['column_name' => $value]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column_mains');
    }
};
