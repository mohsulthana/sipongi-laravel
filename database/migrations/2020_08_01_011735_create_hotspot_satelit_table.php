<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateHotspotSatelitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotspot_satelit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('date_hotspot')->nullable();
            $table->double('x')->nullable();
            $table->double('y')->nullable();
            $table->string('sumber')->nullable();
            $table->string('source')->nullable();
            $table->integer('confidence')->nullable()->default(100);
            $table->double('brightness')->nullable();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('provinsi')->nullable();
            $table->char('kotakab_id', 4)->nullable();
            $table->string('kabkota')->nullable();
            $table->char('kecamatan_id', 7)->nullable();
            $table->string('kecamatan')->nullable();
            $table->char('kelurahan_id', 10)->nullable();
            $table->string('desa')->nullable();
            $table->string('kawasan')->nullable();
            $table->integer('counter')->nullable();
            $table->boolean('publikasi')->default(true);
            $table->boolean('hotspot_daily_update')->default(false);
            $table->string('fungsi_kawasan')->nullable();
            $table->string('sk_kawasan')->nullable();
            $table->string('nama_hti')->nullable();
            $table->string('nama_ha')->nullable();
            $table->string('nama_kebun')->nullable();
            $table->string('sumber2')->nullable();
            $table->point('geom', 'GEOMETRY', 4326)->nullable();
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
        Schema::dropIfExists('hotspot_satelit');
    }
}
