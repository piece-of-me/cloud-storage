<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'uf_user_idx');
            $table->foreign('user_id', 'uf_post_fk')->on('users')->references('id');

            $table->unsignedBigInteger('file_id');
            $table->index('file_id', 'uf_file_idx');
            $table->foreign('file_id', 'uf_file_fk')->on('files')->references('id');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->index('parent_id', 'uf_parent_idx');
            $table->foreign('parent_id', 'uf_parent_fk')->on('files')->references('id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
