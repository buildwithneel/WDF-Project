<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

/**
 * PaymentController
 *
 * Handles the simulated payment flow.
 * IMPORTANT: No real payment gateway is used. Payment is simulated.
 *
 * Flow: Hold → Payment Page (5-min countdown) → Pay/Cancel
 */
class PaymentController extends Controller
{
    /**
     * Show the payment page.
     * GET /payment/{booking}
     *
     * Displays booking details, fare, and a 5-minute countdown timer.
     * Only the booking's owner can view this page.
     */
    public function show(Booking $booking)
    {
        // Authorization: only the owner can view their payment page
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // If booking is no longer pending, redirect accordingly
        if ($booking->payment_status === 'paid') {
            return redirect()->route('ticket.show', $booking);
        }

        if (in_array($booking->payment_status, ['cancelled', 'failed'])) {
            return redirect()->route('home')
                ->with('error', 'This booking is no longer active.');
        }

        // Eager-load all nested relationships needed for the payment page
        $booking->load(['seat.schedule.bus', 'seat.schedule.route']);

        return view('payment.show', compact('booking'));
    }

    /**
     * Process simulated payment (Pay Now button).
     * POST /payment/{booking}/pay
     *
     * Checks hold hasn't expired, then confirms the booking.
     * Uses lockForUpdate() to prevent race conditions on confirmation.
     */
    public function pay(Booking $booking)
    {
        // Authorization check
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($booking) {
                // Lock the seat row for update to prevent concurrent changes
                $seat = $booking->seat()->lockForUpdate()->first();

                // Check if hold has expired
                if (now()->gt($seat->held_until)) {
                    // Release the expired hold
                    $seat->update(['status' => 'available', 'held_until' => null]);
                    $booking->update(['payment_status' => 'failed']);

                    throw new \Exception('expired');
                }

                // Verify booking is still pending
                if ($booking->payment_status !== 'pending') {
                    throw new \Exception('not_pending');
                }

                // CONFIRM: mark seat as booked and booking as paid
                $seat->update(['status' => 'booked', 'held_until' => null]);
                $booking->update([
                    'payment_status' => 'paid',
                    'booked_at'      => now(),
                ]);
            });

            return redirect()->route('ticket.show', $booking)
                ->with('success', 'Payment successful! Your ticket is confirmed.');

        } catch (\Exception $e) {
            if ($e->getMessage() === 'expired') {
                return redirect()->route('home')
                    ->with('error', 'Time expired! Your hold was released. Please book again.');
            }
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }

    /**
     * Cancel the booking from the payment page.
     * POST /payment/{booking}/cancel
     *
     * Releases the hold and marks booking as cancelled.
     */
    public function cancel(Booking $booking)
    {
        // Authorization check
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->payment_status !== 'pending') {
            return redirect()->route('home');
        }

        DB::transaction(function () use ($booking) {
            // Release the seat — make it available again
            $booking->seat->update([
                'status'     => 'available',
                'held_until' => null,
            ]);

            $booking->update(['payment_status' => 'cancelled']);
        });

        return redirect()->route('home')
            ->with('info', 'Booking cancelled. The seat is now available again.');
    }
}
