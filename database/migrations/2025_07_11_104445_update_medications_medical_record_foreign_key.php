<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // Remove existing foreign key constraint
            $table->dropForeign('medications_medical_record_id_foreign');

            // Add new foreign key with cascade delete
            $table->foreign('medical_record_id')
                ->references('id')
                ->on('medical_records')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropForeign(['medications_medical_record_id_foreign']);
            $table->foreign('medical_record_id')
                ->references('id')
                ->on('medical_records')
                ->onDelete('restrict');
        });
    }
};
