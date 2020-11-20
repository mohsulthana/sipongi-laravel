<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubPerpuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pub_perpu_kategori', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
            $table->index(['name']);
        });

        Schema::create('pub_perpu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kategori_id')->index();
            $table->string('slug')->nullable();
            $table->string('nomor')->nullable();
            $table->string('title')->nullable();
            $table->string('tipe')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
            $table->index(['title']);
            $table->index(['slug', 'kategori_id']);
            $table->index(['title', 'kategori_id']);

            $table->foreign('kategori_id')->references('id')->on('pub_perpu_kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pub_perpu');
        Schema::dropIfExists('pub_perpu_kategori');
    }
}
