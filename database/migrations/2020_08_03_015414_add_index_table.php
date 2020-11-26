<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotspot_satelit', function (Blueprint $table) {
            $table->index(['date_hotspot', 'x', 'y', 'sumber']);
            $table->index(['date_hotspot', 'sumber']);
            $table->index(['date_hotspot', 'confidence']);
            $table->index(['sumber', 'provinsi_id']);
            $table->index([DB::raw('date(date_hotspot)')]);
            $table->index(['confidence', 'source', 'provinsi_id', 'hotspot_daily_update']);
            $table->index(['confidence', 'source', 'provinsi_id', DB::raw('date(date_hotspot)')]);
            $table->index([DB::raw('date(date_hotspot)'), 'x', 'y', 'sumber']);
            $table->spatialIndex([DB::raw('ST_Transform(ST_SetSRID(ST_Point(x,y), 4326),3857)')], 'hotspot_satelit_transform_geom_idx');
            $table->index(['date_hotspot', 'publikasi', 'confidence', 'provinsi_id', 'kotakab_id']);
        });
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->index(['provider']);
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
            $table->dropIndex(['date_hotspot', 'x', 'y', 'sumber']);
            $table->dropIndex(['date_hotspot', 'sumber']);
            $table->dropIndex(['date_hotspot', 'confidence']);
            $table->dropIndex(['sumber', 'provinsi_id']);
            $table->dropIndex([DB::raw('date(date_hotspot)')]);
            $table->dropIndex(['confidence', 'source', 'provinsi_id', 'hotspot_daily_update']);
            $table->dropIndex(['confidence', 'source', 'provinsi_id', DB::raw('date(date_hotspot)')]);
            $table->dropIndex([DB::raw('date(date_hotspot)'), 'x', 'y', 'sumber']);
            $table->dropIndex('hotspot_satelit_transform_geom_idx');
            $table->dropIndex(['date_hotspot', 'publikasi', 'confidence', 'provinsi_id', 'kotakab_id']);
        });
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropIndex(['provider']);
        });
    }
}
