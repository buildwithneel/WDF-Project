<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Route Model
 *
 * Represents a travel route from source to destination with a fare.
 * Note: 'routes' is also a Laravel concept (URL routes), but this is an Eloquent Model.
 */
class Route extends Model
{
    use HasFactory;

    protected $fillable = ['source', 'destination', 'fare'];

    /**
     * Relationship: Route has many Schedules.
     * A route (e.g. Ahmedabad → Mumbai) can have many scheduled trips.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
