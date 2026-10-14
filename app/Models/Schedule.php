<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Schedule Model
 *
 * Represents a specific bus trip: a bus assigned to a route on a date/time.
 * When a schedule is created, its seats are auto-generated via the 'created' observer.
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'route_id', 'travel_date', 'departure_time'];

    /**
     * Cast travel_date to a Carbon date for easy manipulation.
     * Cast departure_time as string (time columns).
     */
    protected $casts = [
        'travel_date' => 'date',
    ];

    /**
     * Relationship: Schedule belongs to one Bus.
     * Access via: $schedule->bus->name
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Relationship: Schedule belongs to one Route.
     * Access via: $schedule->route->source
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Relationship: Schedule has many Seats.
     * Access via: $schedule->seats (returns collection of Seat models)
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Auto-generate seats when a new Schedule is created.
     * Called by the 'boot' method — this is the Eloquent Observer pattern.
     * Generates seats like A1, A2, A3, A4, B1, B2, ... up to total_seats.
     */
    protected static function booted(): void
    {
        static::created(function (Schedule $schedule) {
            $totalSeats = $schedule->bus->total_seats;
            $rows = range('A', 'Z');  // Row letters: A, B, C, ...
            $seatsPerRow = 4;
            $seats = [];

            for ($i = 0; $i < $totalSeats; $i++) {
                $rowIndex  = intdiv($i, $seatsPerRow);         // 0=A, 1=B, 2=C ...
                $seatInRow = ($i % $seatsPerRow) + 1;          // 1, 2, 3, 4
                $seatNo    = $rows[$rowIndex] . $seatInRow;     // e.g. A1, B3

                $seats[] = [
                    'schedule_id' => $schedule->id,
                    'seat_no'     => $seatNo,
                    'status'      => 'available',
                    'held_until'  => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            // Bulk insert for performance (single SQL INSERT)
            Seat::insert($seats);
        });
    }
}
