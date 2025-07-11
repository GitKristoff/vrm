<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('veterinarians', function (Blueprint $table) {
            // Remove unnecessary columns
            $table->dropColumn(['name', 'email', 'password']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veterinarians', function (Blueprint $table) {
            // Add back the columns in case of rollback
            $table->string('name')->after('user_id');
            $table->string('email')->after('name');
            $table->string('password')->after('email');
        });
    }
};
