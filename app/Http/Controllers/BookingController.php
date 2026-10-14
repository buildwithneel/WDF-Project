<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BookingController
 *
 * Handles seat booking (hold) and booking cancellation.
 *
 * KEY ALGORITHM: Pessimistic locking to prevent double-booking.
 * Uses DB::transaction + lockForUpdate() so concurrent requests are serialized
 * at the database level — only ONE user can book the same seat.
 */
class BookingController extends Controller
{
    /**
     * Hold a seat and create a pending booking.
     * POST /book
     *
     * DOUBLE-BOOKING PREVENTION:
     * lockForUpdate() issues SELECT ... FOR UPDATE in MySQL.
     * If two requests arrive simultaneously, the second one BLOCKS until the
     * first transaction commits. By then, status will be 'held' and the second
     * request will redirect with "Seat already taken".
     */
    public function store(Request $request)
    {
        // Validate that seat_id is present and exists in the seats table
        $request->validate([
            'seat_id' => 'required|exists:seats,id',
        ]);

        try {
            // Wrap everything in a database transaction for atomicity
            $booking = DB::transaction(function () use ($request) {

                // lockForUpdate() = SELECT ... FOR UPDATE
                // This row-level lock prevents any other transaction from reading
                // or modifying this seat row until this transaction commits.
                $seat = Seat::lockForUpdate()->findOrFail($request->seat_id);

                // Check if seat is available (considering expired holds)
                $isAvailable = $seat->status === 'available'
                    || ($seat->status === 'held' && $seat->held_until < now());

                if (! $isAvailable) {
                    // Another user got here first — abort and inform
                    throw new \Exception('Seat already taken');
                }

                // Hold the seat for 5 minutes
                $seat->update([
                    'status'     => 'held',
                    'held_until' => now()->addMinutes(5),
                ]);

                // Create a pending booking record for the authenticated user
                return Booking::create([
                    'user_id'        => auth()->id(),
                    'seat_id'        => $seat->id,
                    'payment_status' => 'pending',
                ]);
            });

            // Redirect to payment page with the new booking
            return redirect()->route('payment.show', $booking)
                ->with('success', 'Seat held for 5 minutes. Please complete payment.');

        } catch (\Exception $e) {
            // Handle both "seat taken" and database errors gracefully
            return back()->with('error', $e->getMessage() === 'Seat already taken'
                ? 'Sorry, that seat was just taken by another user. Please choose another.'
                : 'Booking failed. Please try again.');
        }
    }

    /**
     * Show all bookings for the logged-in user.
     * GET /my-bookings
     *
     * Eager-loads seat → schedule → bus and route to avoid N+1 queries.
     */
    public function myBookings()
    {
        $bookings = auth()->user()
            ->bookings()
            ->with(['seat.schedule.bus', 'seat.schedule.route'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Cancel a paid booking (future trips only).
     * POST /bookings/{booking}/cancel
     *
     * Releases the seat and marks booking as cancelled.
     * Only the booking's owner can cancel (authorization check).
     */
    public function cancel(Booking $booking)
    {
        // Authorization: only the booking owner can cancel
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow cancellation of paid bookings
        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Only paid bookings can be cancelled.');
        }

        // Also check if travel date is in the future
        $travelDate = $booking->seat->schedule->travel_date;
        if ($travelDate->isPast()) {
            return back()->with('error', 'Cannot cancel a past booking.');
        }

        DB::transaction(function () use ($booking) {
            // Release the seat back to available
            $booking->seat->update([
                'status'     => 'available',
                'held_until' => null,
            ]);

            // Mark booking as cancelled
            $booking->update(['payment_status' => 'cancelled']);
        });

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
