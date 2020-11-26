<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_daops')->nullable();
            $table->string('nama_daops')->nullable();
            $table->char('provinsi_id', 2)->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->double('latitude')->default(0);
            $table->double('longitude')->default(0);
            $table->integer('jumlah_regu')->default(0);
            $table->text('sapras')->nullable();
            $table->text('wilayah_kerja')->nullable();
            $table->string('satuan_kerja')->nullable();
            $table->string('no_sk')->nullable();
            $table->date('tgl_sk')->nullable();
            $table->integer('jumlah_gajah')->nullable();
            $table->integer('jumlah_pawang')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('daops');
    }
}
