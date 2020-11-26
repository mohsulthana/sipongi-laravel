<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreatePetaKawasanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peta_kawasan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('placemark_id')->nullable();
            $table->string('placemark_name')->nullable();
            $table->string('fungsi')->nullable();
            $table->string('fungsi_kawasan')->nullable();
            $table->string('sk_kawasan')->nullable();
            $table->date('tgl_kawasan')->nullable();
            $table->double('luas_kawasan')->default(0);
            $table->string('dpcls')->nullable();
            $table->text('keterangan')->nullable();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('provinsi')->nullable();
            $table->double('shape_leng')->nullable();
            $table->double('shape_area')->nullable();
            $table->longText('poligon')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade');
            $table->spatialIndex(['geom']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peta_kawasan');
    }
}
