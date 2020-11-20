<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('default_pass')->after('status')->default(false);
            $table->integer('regional_id')->after('default_pass')->nullable();
            $table->char('provinsi_id', 2)->after('regional_id')->nullable();
            $table->uuid('daops_id')->after('provinsi_id')->nullable();
            $table->string('unit_kerja')->after('daops_id')->nullable();
            $table->text('keterangan')->after('unit_kerja')->nullable();

            $table->foreign('regional_id')->references('id')->on('regional')->onDelete('cascade');
            $table->foreign('provinsi_id')->references('id')->on('provinsi')->onDelete('cascade');
            $table->foreign('daops_id')->references('id')->on('daops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['regional_id']);
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['daops_id']);
            $table->dropColumn('default_pass');
            $table->dropColumn('regional_id');
            $table->dropColumn('provinsi_id');
            $table->dropColumn('daops_id');
            $table->dropColumn('unit_kerja');
            $table->dropColumn('keterangan');
        });
    }
}
