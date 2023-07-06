<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('operational_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });


        $data = [
            ['id' => 1, 'name' => 'OSE', 'short_name' => 'OSE'],
            ['id' => 2, 'name' => 'Golac', 'short_name' => 'GOL'],
            ['id' => 3, 'name' => 'EMC', 'short_name' => 'EMC'],
            ['id' => 4, 'name' => 'Stokis', 'short_name' => 'BLT'],
            ['id' => 5, 'name' => 'Agent', 'short_name' => 'BLT'],
            ['id' => 6, 'name' => 'Staff Purchase', 'short_name' => 'OTH'],
            ['id' => 7, 'name' => 'Direct Sales', 'short_name' => 'OTH'],
            ['id' => 8, 'name' => 'FOC', 'short_name' => 'FOC'],
            ['id' => 9, 'name' => 'Ecommerce', 'short_name' => 'ECM'],
            ['id' => 10, 'name' => 'CSE', 'short_name' => 'CRA'],
            ['id' => 11, 'name' => 'Sales Center', 'short_name' => 'OTH'],
            ['id' => 12, 'name' => 'Marketplace', 'short_name' => 'MKT'],
            ['id' => 13, 'name' => 'Marketplace', 'short_name' => 'MKT'],
            ['id' => 14, 'name' => 'Whatomation', 'short_name' => 'OTH'],
            ['id' => 15, 'name' => 'NOSE', 'short_name' => 'BLT'],
            ['id' => 16, 'name' => 'Blast', 'short_name' => 'BLT'],
            ['id' => 17, 'name' => 'VOSE', 'short_name' => 'OSE'],
            ['id' => 18, 'name' => 'GO Shop', 'short_name' => 'GSP'],
        ];

        DB::table('operational_models')->insert($data);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operational_models');
    }
};
