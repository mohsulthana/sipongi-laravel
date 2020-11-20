<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MStaack\LaravelPostgis\Schema\Blueprint;

class CreateSumCentroidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sum_centroid', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('provinsi_id')->nullable();
            $table->string('provinsi')->nullable();
            $table->char('kabid', 2)->nullable();
            $table->string('kabkota')->nullable();
            $table->text('cp')->nullable();
            $table->text('cka')->nullable();
            $table->point('gcp', 'GEOMETRY', 4326)->nullable();
            $table->point('gcka', 'GEOMETRY', 4326)->nullable();
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
        Schema::dropIfExists('sum_centroid');
    }
}
