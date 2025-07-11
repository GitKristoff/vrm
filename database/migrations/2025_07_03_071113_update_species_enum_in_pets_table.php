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
        DB::statement("ALTER TABLE pets MODIFY species ENUM(
            'Dog',
            'Cat',
            'Bird',
            'Rabbit',
            'Rodent',
            'Reptile',
            'Fish',
            'Other'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to original enum values if needed
        DB::statement("ALTER TABLE pets MODIFY species ENUM(
            'Dog',
            'Cat',
            'Bird',
            'Rodent',
            'Reptile',
            'Other'
        )");

        // Convert Rabbits to Rodents when rolling back
        DB::table('pets')
            ->where('species', 'Rabbit')
            ->update(['species' => 'Rodent']);
    }
};
