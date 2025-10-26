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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('availability_id')->constrained()->onDelete('cascade');
            $table->boolean('is_hold')->default(false);
            $table->dateTime('cycle_start_utc');
            $table->enum('status', ['booked', 'completed', 'cancelled'])->default('booked');
            $table->timestamps();

            $table->unique(['student_id', 'availability_id']); // prevent double-booking same slot

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
