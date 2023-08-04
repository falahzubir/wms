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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->foreign("product_id")->references("id")->on("products");
            $table->unsignedBigInteger("company_id");
            $table->foreign("company_id")->references("id")->on("companies");
            $table->unsignedBigInteger("storage_cond")->comment("1: Ambient, 2: Air-condition, 3: Chill, 4: Frozen");
            $table->integer("category_id")->comment("1: Consumable, 2: Finish Goods, 3: Packaging Material");
            $table->integer("sub_category_id")->nullable()->comment("1: GMS - General Merchandise, 2: Food, 3: Non-Food, 4: Controlled Item");
            $table->boolean("expiry")->comment("1: Yes, 0: No");
            $table->boolean("shelf_life")->nullable()->comment("1: Yes, 0: No");
            $table->integer("shelf_life_period")->nullable()->comment("days");
            $table->boolean("qa_qc")->nullable()->comment("1: Yes, 0: No");
            $table->string("image_path");
            $table->float("length")->comment("m");
            $table->float("width")->comment("m");
            $table->float("height")->comment("m");
            $table->float("weight")->comment("g");
            $table->integer("case_pack_carton")->nullable()->comment("pcs");
            $table->integer("case_pack_box")->nullable()->comment("pcs");
            $table->integer("case_pack_unit")->nullable()->comment("pcs");
            $table->integer("tie");
            $table->integer("high");
            $table->integer("pallet_qty");
            $table->float("carton_length")->nullable()->comment("m");
            $table->float("carton_width")->nullable()->comment("m");
            $table->float("carton_height")->nullable()->comment("m");
            $table->float("carton_weight")->nullable()->comment("kg");
            $table->integer("container_load")->nullable()->comment("pcs");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("product_details");
    }
};
