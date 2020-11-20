<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLuasKebakaranTahunanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('luas_kebakaran_tahunan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('provinsi_id', 2)->nullable();
            $table->integer('tahun')->nullable();
            $table->double('luas_kebakaran')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('luas_kebakaran_tahunan');
    }
}
