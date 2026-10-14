<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration: Creates the routes table (source → destination with fare)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('source');                            // e.g. "Ahmedabad"
            $table->string('destination');                       // e.g. "Mumbai"
            $table->decimal('fare', 8, 2);                      // ticket price in INR
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
