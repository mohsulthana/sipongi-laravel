<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSumWilayahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sum_wilayah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('provinsi_id')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabid')->nullable();
            $table->string('kabkota')->nullable();
            $table->string('kecid')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desid')->nullable();
            $table->string('desa')->nullable();
            $table->string('nama')->nullable();
            $table->string('jenis')->nullable();
            $table->string('kategori')->nullable();
            $table->json('meta')->nullable();
            $table->longText('kml')->nullable();
            $table->string('sumber')->nullable();
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
        Schema::dropIfExists('sum_wilayah');
    }
}
