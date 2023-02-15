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
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('path');
            $table->index('parent_id', 'files_parent_idx');
            $table->foreign('parent_id', 'files_parent_fk')->on('files')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('files_parent_fk');
            $table->dropIndex('files_parent_idx');
            $table->dropColumn('parent_id');
        });
    }
};
