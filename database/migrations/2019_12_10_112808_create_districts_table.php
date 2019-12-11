<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('ProvinceName');
                $table->integer('ProvinceID');
                $table->string('DistrictName');
                $table->string('DistrictCode');
                $table->integer('DistrictID');
                $table->string('WardName');
                $table->string('Code');
                $table->string('WardCode');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
}
