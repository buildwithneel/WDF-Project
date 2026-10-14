<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration: Creates schedules table linking a bus to a route on a specific date/time
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // Foreign key to buses — cascade delete removes all schedules if bus deleted
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            // Foreign key to routes
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->date('travel_date');                         // e.g. 2025-01-15
            $table->time('departure_time');                      // e.g. 08:00:00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
