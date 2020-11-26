<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateProvinsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('nama_provinsi')->nullable();
            $table->string('pulau')->nullable();
            $table->double('x')->default(0);
            $table->double('y')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('regional_id')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->integer('old_id')->nullable();

            $table->foreign('regional_id')->references('id')->on('regional')->onDelete('cascade');
            $table->spatialIndex(['geom']);
        });

        Schema::create('kotakab', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('nama')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->json('meta')->nullable();
            $table->string('sumber')->nullable();

            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade');
            $table->spatialIndex(['geom']);
        });

        Schema::create('kecamatan', function (Blueprint $table) {
            $table->char('id', 7)->primary();
            $table->char('kotakab_id', 4)->nullable();
            $table->string('nama')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();

            $table->foreign('kotakab_id')->references('id')->on('kotakab')->onDelete('cascade');
            $table->spatialIndex(['geom']);
        });

        Schema::create('kelurahan', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->char('kecamatan_id', 7)->nullable();
            $table->string('nama')->nullable();
            $table->multipolygon('geom', 'GEOMETRY', 4326)->nullable();
            $table->json('meta')->nullable();
            $table->string('sumber')->nullable();

            $table->foreign('kecamatan_id')->references('id')->on('kecamatan')->onDelete('cascade');
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
        Schema::dropIfExists('kelurahan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kotakab');
        Schema::dropIfExists('provinsi');
    }
}
