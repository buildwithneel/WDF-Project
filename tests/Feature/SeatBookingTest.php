<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SeatBookingTest
 *
 * Feature test that verifies the double-booking prevention mechanism.
 *
 * KEY TEST: Two users simultaneously booking the same seat.
 * Expected result: exactly ONE succeeds (seat held), ONE fails (seat taken).
 *
 * Run with: php artisan test
 */
class SeatBookingTest extends TestCase
{
    // RefreshDatabase: wraps each test in a transaction and rolls back after.
    // This keeps tests isolated — no test data leaks between tests.
    use RefreshDatabase;

    /**
     * Helper: create a complete schedule with seats for testing.
     */
    private function createScheduleWithSeat(): array
    {
        $bus = Bus::create(['name' => 'Test Bus', 'type' => 'AC', 'total_seats' => 4]);
        $route = Route::create(['source' => 'A', 'destination' => 'B', 'fare' => 100]);

        // Schedule creation auto-generates 4 seats (A1–A4) via model observer
        $schedule = Schedule::create([
            'bus_id'         => $bus->id,
            'route_id'       => $route->id,
            'travel_date'    => now()->addDay()->toDateString(),
            'departure_time' => '08:00',
        ]);

        $seat = $schedule->seats()->first(); // grab the first available seat

        return [$schedule, $seat];
    }

    /**
     * Test: A single authenticated user can book an available seat.
     */
    public function test_user_can_book_available_seat(): void
    {
        [, $seat] = $this->createScheduleWithSeat();
        $user = User::factory()->create(['role' => 'user']);

        // POST to /book as authenticated user
        $response = $this->actingAs($user)->post('/book', ['seat_id' => $seat->id]);

        // Should redirect to payment page (not back with error)
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Seat should now be 'held'
        $this->assertDatabaseHas('seats', [
            'id'     => $seat->id,
            'status' => 'held',
        ]);

        // A pending booking should exist
        $this->assertDatabaseHas('bookings', [
            'user_id'        => $user->id,
            'seat_id'        => $seat->id,
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Test: Two users trying to book the SAME seat — only ONE succeeds.
     *
     * This is the core double-booking prevention test.
     * We simulate concurrency by making two sequential requests targeting the same seat.
     * The lockForUpdate() in BookingController ensures the second request
     * sees the seat as 'held' and fails gracefully.
     */
    public function test_two_users_cannot_book_same_seat(): void
    {
        [, $seat] = $this->createScheduleWithSeat();

        $userA = User::factory()->create(['role' => 'user']);
        $userB = User::factory()->create(['role' => 'user']);

        // User A books the seat first — should succeed
        $responseA = $this->actingAs($userA)->post('/book', ['seat_id' => $seat->id]);

        // User B tries to book the same seat — should fail with error
        $responseB = $this->actingAs($userB)->post('/book', ['seat_id' => $seat->id]);

        // User A's booking: redirected to payment (success)
        $responseA->assertRedirect();

        // User B's response: redirected back with an error message
        $responseB->assertRedirect();
        $responseB->assertSessionHas('error');

        // Database: exactly ONE booking created (User A's)
        $this->assertDatabaseCount('bookings', 1);

        // The booking belongs to User A
        $this->assertDatabaseHas('bookings', [
            'user_id'        => $userA->id,
            'seat_id'        => $seat->id,
            'payment_status' => 'pending',
        ]);

        // Seat is 'held' (by User A), not double-booked
        $this->assertDatabaseHas('seats', [
            'id'     => $seat->id,
            'status' => 'held',
        ]);
    }

    /**
     * Test: Unauthenticated users cannot access the booking endpoint.
     */
    public function test_guest_cannot_book_seat(): void
    {
        [, $seat] = $this->createScheduleWithSeat();

        // POST without authentication
        $response = $this->post('/book', ['seat_id' => $seat->id]);

        // Should redirect to login page
        $response->assertRedirect('/login');

        // No bookings should be created
        $this->assertDatabaseCount('bookings', 0);
    }

    /**
     * Test: A held seat cannot be re-booked during the hold period.
     */
    public function test_held_seat_cannot_be_booked(): void
    {
        [, $seat] = $this->createScheduleWithSeat();

        // Manually set seat to held (simulating another user's hold)
        $seat->update([
            'status'     => 'held',
            'held_until' => now()->addMinutes(3), // hold still active
        ]);

        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/book', ['seat_id' => $seat->id]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // No bookings created
        $this->assertDatabaseCount('bookings', 0);
    }

    /**
     * Test: An expired hold CAN be re-booked (seat is effectively available again).
     */
    public function test_expired_hold_seat_can_be_rebooked(): void
    {
        [, $seat] = $this->createScheduleWithSeat();

        // Simulate an expired hold (held_until is in the past)
        $seat->update([
            'status'     => 'held',
            'held_until' => now()->subMinutes(1), // expired
        ]);

        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/book', ['seat_id' => $seat->id]);

        // Should succeed — expired holds are treated as available
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bookings', [
            'user_id'        => $user->id,
            'seat_id'        => $seat->id,
            'payment_status' => 'pending',
        ]);
    }
}
