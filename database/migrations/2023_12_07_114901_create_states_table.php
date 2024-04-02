<?php

use App\Models\State;
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
        Schema::create('states', function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->string('name');
            $table->string('country_code', 3)->nullable();
            $table->unsignedBigInteger('cod_courier_id', false)->nullable();
            $table->unsignedBigInteger('non_cod_courier_id', false)->nullable();
            $table->foreign('cod_courier_id')->references('id')->on('couriers');
            $table->foreign('non_cod_courier_id')->references('id')->on('couriers');
            $table->timestamps();
        });

        foreach(MY_STATES as $key => $value){
            if($key > 0 && $key <= 16){
                $country_code = 'MY';
            }
            elseif($key == 17){
                $country_code = 'SG';
            }
            elseif($key > 17 && $key <= 60){
                $country_code = 'ID';
            }
            else{
                $country_code = null;
            }
            State::create([
                'id' => $key,
                'name' => $value,
                'country_code' => $country_code,
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
        State::truncate();
        Schema::dropIfExists('states');
    }
};
