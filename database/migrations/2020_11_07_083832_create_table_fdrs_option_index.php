<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFdrsOptionIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {	if(!Schema::hasTable('fdrs_option_index'))
        Schema::create('fdrs_option_index', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key','250');
            $table->string('nama','250');

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
        Schema::dropIfExists('fdrs_option_index');
    }
}
