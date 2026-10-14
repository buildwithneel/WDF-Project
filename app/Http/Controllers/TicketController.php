<?php

namespace App\Http\Controllers;

use App\Models\Booking;

/**
 * TicketController
 *
 * Displays the confirmed e-ticket after successful payment.
 * Includes QR code generation and print stylesheet support.
 */
class TicketController extends Controller
{
    /**
     * Show the ticket for a confirmed booking.
     * GET /ticket/{booking}
     *
     * Only the booking owner can view their ticket.
     * Loads all nested relationships needed for the ticket display.
     */
    public function show(Booking $booking)
    {
        // Authorization: only the owner may view this ticket
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this ticket.');
        }

        // Only show ticket for paid bookings
        if ($booking->payment_status !== 'paid') {
            return redirect()->route('home')
                ->with('error', 'Ticket is only available for confirmed bookings.');
        }

        // Eager-load the full chain: booking → seat → schedule → bus + route
        $booking->load(['seat.schedule.bus', 'seat.schedule.route', 'user']);

        return view('ticket.show', compact('booking'));
    }
}
