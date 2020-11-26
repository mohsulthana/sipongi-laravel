<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipeHotspotSatelitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotspot_satelit', function (Blueprint $table) {
            $table->string('tipe')->nullable();
            $table->index(['date_hotspot', 'publikasi', 'confidence_level', 'provinsi_id', 'kotakab_id', 'tipe'], 'hotspot_satelit_with_tipe_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotspot_satelit', function (Blueprint $table) {
            $table->dropColumn('tipe');
            $table->dropIndex('hotspot_satelit_with_tipe_idx');
        });
    }
}
