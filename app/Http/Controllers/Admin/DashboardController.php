<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;

/**
 * Admin\DashboardController
 *
 * Displays the admin dashboard with system-wide statistics.
 * Protected by 'admin' middleware (see routes/web.php).
 */
class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     * GET /admin
     *
     * Aggregates counts and revenue using Eloquent's aggregate methods.
     */
    public function index()
    {
        // Aggregate stats — each is a single optimized SQL query
        $stats = [
            'buses'     => Bus::count(),
            'routes'    => Route::count(),
            'schedules' => Schedule::count(),
            'bookings'  => Booking::count(),
            // SUM fare from paid bookings via chained relationships
            'revenue'   => Booking::where('payment_status', 'paid')
                ->join('seats', 'bookings.seat_id', '=', 'seats.id')
                ->join('schedules', 'seats.schedule_id', '=', 'schedules.id')
                ->join('routes', 'schedules.route_id', '=', 'routes.id')
                ->sum('routes.fare'),
        ];

        // Recent bookings for the activity feed
        $recentBookings = Booking::with(['user', 'seat.schedule.route'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
