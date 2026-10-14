<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SeatController
 *
 * Displays the seat map for a given schedule.
 * Also releases expired holds before rendering (so demo works without scheduler).
 */
class SeatController extends Controller
{
    /**
     * Show the seat map for a schedule.
     * GET /seats/{schedule}
     *
     * 1. Releases expired holds (core maintenance task)
     * 2. Fetches seats ordered by seat_no
     * 3. Groups seats by row letter for the 2+2 grid layout
     */
    public function show(Schedule $schedule)
    {
        // ---------------------------------------------------------------
        // Auto-release expired holds BEFORE rendering the seat map.
        // This ensures the UI is accurate even without the scheduler running.
        // (The scheduled command seats:release-expired does this every minute.)
        // ---------------------------------------------------------------
        $this->releaseExpiredHolds($schedule);

        // Reload seats after release to get fresh status
        $seats = $schedule->seats()
            ->orderBy('seat_no')
            ->get();

        // Group seats by row letter (A, B, C, ...) for the grid view
        // e.g. ['A' => [A1, A2, A3, A4], 'B' => [B1, B2, B3, B4], ...]
        $grouped = $seats->groupBy(fn($seat) => substr($seat->seat_no, 0, 1));

        return view('seats.show', compact('schedule', 'grouped'));
    }

    /**
     * Release all expired held seats for a given schedule.
     * Sets status = 'available', held_until = null.
     * Marks the corresponding pending bookings as 'failed'.
     *
     * Private helper — also called by the seats:release-expired Artisan command.
     */
    private function releaseExpiredHolds(Schedule $schedule): void
    {
        // Find all held seats whose hold time has expired
        $expiredSeats = $schedule->seats()
            ->where('status', 'held')
            ->where('held_until', '<', now())
            ->get();

        foreach ($expiredSeats as $seat) {
            DB::transaction(function () use ($seat) {
                // Mark pending bookings for this seat as failed
                $seat->bookings()
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);

                // Release the seat back to available
                $seat->update([
                    'status'     => 'available',
                    'held_until' => null,
                ]);
            });
        }
    }
}
