<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateRawGeoProvinsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_geo_provinsi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('provinsi_id')->nullable();
            $table->string('nama')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->json('meta')->nullable();
            $table->integer('sumberid')->nullable();
            $table->integer('sumber')->nullable();
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
        Schema::dropIfExists('raw_geo_provinsi');
    }
}
