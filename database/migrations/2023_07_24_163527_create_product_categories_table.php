<?php

use App\Models\ProductCategory;
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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->foreign('product_category_id')->references('id')->on('product_categories');
            $table->unsignedInteger('status')->default(1)->comment('1-Active, 2-Inactive');
            $table->timestamps();
        });

        ProductCategory::insert([
            [
                'name' => 'Consumable',
                'product_category_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'Finish Goods',
                'product_category_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'GMS - General Merchandise',
                'product_category_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Food',
                'product_category_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Non-Food',
                'product_category_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Controlled Item',
                'product_category_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Packaging Material',
                'product_category_id' => null,
                'status' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
};
