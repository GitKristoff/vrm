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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('veterinarian_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained();
            $table->date('record_date');
            $table->text('subjective_notes')->nullable(); // SOAP format
            $table->text('objective_notes')->nullable(); // SOAP format
            $table->text('assessment')->nullable(); // SOAP format
            $table->text('plan')->nullable(); // SOAP format
            $table->float('temperature')->nullable();
            $table->float('heart_rate')->nullable();
            $table->float('respiratory_rate')->nullable();
            $table->float('weight')->nullable();
            $table->text('vaccination_history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
