<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration: Creates the buses table
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name');                               // e.g. "Gujarat Express"
            $table->enum('type', ['AC', 'Non-AC', 'Sleeper']);   // bus comfort type
            $table->unsignedInteger('total_seats');              // e.g. 40
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
