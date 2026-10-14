<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Seat Model
 *
 * Represents a single seat in a schedule.
 * Status lifecycle: available → held (with held_until) → booked
 *
 * KEY CONCEPT: The 'held' status + lockForUpdate() prevents double-booking.
 */
class Seat extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'seat_no', 'status', 'held_until'];

    /**
     * Cast held_until to a Carbon datetime for easy comparison with now()
     */
    protected $casts = [
        'held_until' => 'datetime',
    ];

    /**
     * Relationship: Seat belongs to one Schedule.
     * Access via: $seat->schedule->bus->name
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Relationship: Seat has many Bookings.
     * Normally 1 active booking, but history may include cancelled ones.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Helper: get the latest (most recent) booking for this seat.
     * Used on the payment and ticket pages.
     */
    public function latestBooking()
    {
        return $this->hasOne(Booking::class)->latestOfMany();
    }

    /**
     * Helper: check if the hold has expired.
     * A seat is effectively 'available' again if held_until < now().
     */
    public function isHoldExpired(): bool
    {
        return $this->status === 'held'
            && $this->held_until !== null
            && now()->gt($this->held_until);
    }
}
