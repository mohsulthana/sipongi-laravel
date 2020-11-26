<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotspotDailyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotspot_daily', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('bulan')->nullable();
            $table->char('provinsi_id', 2)->nullable();
            $table->string('sumber')->nullable();
            for ($x = 1; $x <= 31; $x++) {
                $table->integer("t$x")->default(0);
            }
            $table->timestamps();
            $table->softDeletes();

            $table->index(['bulan', 'provinsi_id', 'sumber']);

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
        Schema::dropIfExists('hotspot_daily');
    }
}
