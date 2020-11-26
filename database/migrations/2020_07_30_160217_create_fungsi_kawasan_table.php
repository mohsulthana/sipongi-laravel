<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateFungsiKawasanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fungsi_kawasan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('placemark_id')->nullable();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('provinsi')->nullable();
            $table->char('kotakab_id', 4)->nullable();
            $table->string('kabkota')->nullable();
            $table->char('kecamatan_id', 7)->nullable();
            $table->string('kecamatan')->nullable();
            $table->char('kelurahan_id', 10)->nullable();
            $table->string('desa')->nullable();
            $table->string('kawasan')->nullable();
            $table->string('nama_hti')->nullable();
            $table->string('nama_ha')->nullable();
            $table->string('nama_kebun')->nullable();
            $table->longText('poligon')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade');
            $table->foreign('kotakab_id')->references('id')->on('kotakab')->onDelete('cascade');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatan')->onDelete('cascade');
            $table->foreign('kelurahan_id')->references('id')->on('kelurahan')->onDelete('cascade');
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
        Schema::dropIfExists('fungsi_kawasan');
    }
}
