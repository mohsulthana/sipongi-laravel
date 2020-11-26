<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaleriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pub_galeri', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->string('tipe')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
        });

        Schema::create('pub_galeri_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('galeri_id')->index();
            $table->string('slug')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('image')->nullable();
            $table->boolean('publish')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);

            $table->foreign('galeri_id')->references('id')->on('pub_galeri')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pub_galeri_detail');
        Schema::dropIfExists('pub_galeri');
    }
}
