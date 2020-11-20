<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubDokumenLainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pub_dokumen_lain', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->boolean('private')->default(false);
            $table->string('tipe')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
            $table->index(['title']);
            $table->index(['private']);
            $table->index(['slug', 'private']);
            $table->index(['title', 'private']);
            $table->index(['slug', 'private', 'user_id']);
            $table->index(['title', 'private', 'user_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pub_dokumen_lain');
    }
}
