<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Bus Model
 *
 * Represents a bus vehicle with a type and seat capacity.
 * Uses Eloquent ORM — each property maps to a database column.
 */
class Bus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'total_seats'];

    /**
     * Relationship: Bus has many Schedules.
     * One bus can run multiple trips on different dates/routes.
     * Eloquent uses 'bus_id' FK on schedules table automatically.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
