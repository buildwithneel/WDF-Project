<?php

namespace App\Console\Commands;

use App\Models\Seat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Artisan Command: seats:release-expired
 *
 * Releases all held seats whose hold has expired.
 * Run automatically every minute via the scheduler in routes/console.php.
 * Also run manually: php artisan seats:release-expired
 *
 * This prevents ghost holds from blocking seat availability
 * when users abandon the payment page without cancelling.
 */
class ReleaseExpiredSeats extends Command
{
    /**
     * The console command signature (name + arguments/options).
     * Usage: php artisan seats:release-expired
     */
    protected $signature = 'seats:release-expired';

    /**
     * Description shown in: php artisan list
     */
    protected $description = 'Release held seats whose 5-minute hold has expired';

    /**
     * Execute the command logic.
     */
    public function handle(): int
    {
        // Find all held seats where the hold timestamp is in the past
        $expiredSeats = Seat::where('status', 'held')
            ->where('held_until', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredSeats as $seat) {
            DB::transaction(function () use ($seat, &$count) {
                // Mark any pending bookings for this seat as 'failed'
                $seat->bookings()
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'failed']);

                // Release the seat back to 'available'
                $seat->update([
                    'status'     => 'available',
                    'held_until' => null,
                ]);

                $count++;
            });
        }

        // Output result to console
        $this->info("Released {$count} expired seat hold(s).");

        return Command::SUCCESS;
    }
}
