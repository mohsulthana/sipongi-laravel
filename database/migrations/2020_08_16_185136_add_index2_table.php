<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndex2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotspot_satelit', function (Blueprint $table) {
            $table->index(['date_hotspot', 'confidence_level']);
            $table->index(['confidence_level', 'source', 'provinsi_id', 'hotspot_daily_update']);
            $table->index(['confidence_level', 'source', 'provinsi_id', DB::raw('date(date_hotspot)')]);
            $table->index(['date_hotspot', 'publikasi', 'confidence_level', 'provinsi_id', 'kotakab_id']);
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
            $table->dropIndex(['date_hotspot', 'confidence_level']);
            $table->dropIndex(['confidence_level', 'source', 'provinsi_id', 'hotspot_daily_update']);
            $table->dropIndex(['confidence_level', 'source', 'provinsi_id', DB::raw('date(date_hotspot)')]);
            $table->dropIndex(['date_hotspot', 'publikasi', 'confidence_level', 'provinsi_id', 'kotakab_id']);
        });
    }
}
