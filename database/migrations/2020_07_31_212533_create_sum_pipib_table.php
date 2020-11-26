<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateSumPipibTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sum_pipib', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('provinsi')->nullable();
            $table->char('kotakab_id', 4)->nullable();
            $table->string('kabkota')->nullable();
            $table->char('kecamatan_id', 7)->nullable();
            $table->string('kecamatan')->nullable();
            $table->char('kelurahan_id', 10)->nullable();
            $table->string('desa')->nullable();
            $table->string('nama')->nullable();
            $table->string('jenis')->nullable();
            $table->string('kategori')->nullable();
            $table->json('meta')->nullable();
            $table->longText('kml')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->string('sumber')->nullable();
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
        Schema::dropIfExists('sum_pipib');
    }
}
