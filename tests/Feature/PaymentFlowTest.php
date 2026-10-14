<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(): array
    {
        $user = User::factory()->create(['role' => 'user']);
        $bus = Bus::create(['name' => 'Express', 'type' => 'AC', 'total_seats' => 4]);
        $route = Route::create(['source' => 'Mumbai', 'destination' => 'Pune', 'fare' => 500]);
        $schedule = Schedule::create([
            'bus_id'         => $bus->id,
            'route_id'       => $route->id,
            'travel_date'    => now()->addDay()->toDateString(),
            'departure_time' => '10:00',
        ]);
        $seat = $schedule->seats()->first();
        $seat->update([
            'status'     => 'held',
            'held_until' => now()->addMinutes(5),
        ]);

        $booking = Booking::create([
            'user_id'           => $user->id,
            'schedule_id'       => $schedule->id,
            'seat_id'           => $seat->id,
            'booking_reference' => 'BKG-TEST1234',
            'payment_status'    => 'pending',
            'total_fare'        => 500,
            'booked_at'         => now(),
        ]);

        return [$user, $booking, $seat];
    }

    public function test_user_can_pay_and_confirm_booking(): void
    {
        [$user, $booking, $seat] = $this->createBooking();

        $response = $this->actingAs($user)->post("/payment/{$booking->id}/pay");

        $response->assertRedirect("/ticket/{$booking->id}");
        $this->assertDatabaseHas('bookings', [
            'id'             => $booking->id,
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseHas('seats', [
            'id'     => $seat->id,
            'status' => 'booked',
        ]);
    }

    public function test_user_can_cancel_pending_booking(): void
    {
        [$user, $booking, $seat] = $this->createBooking();

        $response = $this->actingAs($user)->post("/payment/{$booking->id}/cancel");

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('bookings', [
            'id'             => $booking->id,
            'payment_status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('seats', [
            'id'         => $seat->id,
            'status'     => 'available',
            'held_until' => null,
        ]);
    }
}
