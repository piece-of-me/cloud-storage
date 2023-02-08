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
            $table->unsignedBigInteger('type_id')->nullable()->after('name');

            $table->index('type_id', 'file_file_type_idx');
            $table->foreign('type_id', 'file_file_type_fk')->on('file_types')->references('id');
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
            $table->dropForeign('file_file_type_fk');
            $table->dropIndex('file_file_type_idx');
            $table->dropColumn('type_id');
        });
    }
};
