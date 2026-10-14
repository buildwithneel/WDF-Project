<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Booking Model
 *
 * Records a user's booking for a seat.
 * Payment status: pending → paid / failed / cancelled.
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'seat_id', 'payment_status', 'booked_at'];

    /**
     * Cast booked_at to Carbon for display formatting
     */
    protected $casts = [
        'booked_at' => 'datetime',
    ];

    /**
     * Relationship: Booking belongs to a User (the passenger)
     * Access via: $booking->user->name
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Booking belongs to a Seat
     * Access via: $booking->seat->seat_no
     * Through seat → schedule → route → bus (chained Eloquent relationships)
     */
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
