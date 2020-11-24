<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFdrsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {	if(!Schema::hasTable('fdrs_data'))
        Schema::create('fdrs_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fdrs_option_wilayah_key','250');
            $table->string('fdrs_option_index_key','250');
            $table->string('fdrs_option_hari_key','250');
            $table->date('date');
            $table->string('image','250');

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
        Schema::dropIfExists('fdrs_data');
    }
}
