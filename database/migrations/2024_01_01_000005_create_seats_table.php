<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration: Creates the seats table — each seat belongs to one schedule
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            // Cascade delete: if schedule is deleted, its seats are deleted too
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->string('seat_no', 10);                       // e.g. A1, B3, C4
            // Status tracks the seat lifecycle: available → held → booked
            $table->enum('status', ['available', 'held', 'booked'])->default('available');
            // held_until: timestamp when the hold expires (null if not held)
            $table->timestamp('held_until')->nullable();
            $table->timestamps();

            // Prevent duplicate seat numbers on the same schedule
            $table->unique(['schedule_id', 'seat_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
