<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeritaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->longText('desc')->nullable();
            $table->string('image')->nullable();
            $table->boolean('publish')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
            $table->index(['title']);
            $table->index(['slug', 'publish']);
            $table->index(['title', 'publish']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('berita');
    }
}
