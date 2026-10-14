<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration: Creates bookings table — links a user to a specific seat
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // The passenger who made the booking
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // The specific seat being booked
            $table->foreignId('seat_id')->constrained('seats')->onDelete('cascade');
            // Payment lifecycle: pending → paid (or failed/cancelled)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])
                  ->default('pending');
            // Timestamp when payment was confirmed
            $table->timestamp('booked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
