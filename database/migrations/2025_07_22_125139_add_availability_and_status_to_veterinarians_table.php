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
            $table->json('working_days')->nullable(); // e.g. ["Monday","Tuesday"]
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('status')->default('in'); // 'in', 'out', 'on leave'
        });
    }

    public function down()
    {
        Schema::table('veterinarians', function (Blueprint $table) {
            $table->dropColumn(['working_days', 'start_time', 'end_time', 'status']);
        });
    }
};
