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
        Schema::table('medical_records', function (Blueprint $table) {
            // Add appointment_id column
            $table->foreignId('appointment_id')
                ->after('veterinarian_id')
                ->nullable()
                ->constrained('appointments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['appointment_id']);

            // Then drop the column
            $table->dropColumn('appointment_id');
        });
    }
};
