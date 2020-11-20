<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmisiCo2Tahunan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emisi_co2_tahunan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('provinsi_id', 2)->nullable();
            $table->integer('tahun')->nullable();
            $table->double('total')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emisi_co2_tahunan');
    }
}
