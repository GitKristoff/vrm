<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAppointmentsStatusToVarchar extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // change enum -> string (varchar)
        Schema::table('appointments', function (Blueprint $table) {
            // Make column nullable temporarily to avoid issues, then modify
            $table->string('status', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // revert to previous (if you had an ENUM, you can restore it here)
        Schema::table('appointments', function (Blueprint $table) {
            // restore to varchar as default fallback — adjust if you prefer enum rollback
            $table->string('status', 50)->nullable(false)->change();
        });
    }
};
