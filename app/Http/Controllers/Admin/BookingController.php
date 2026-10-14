<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

/**
 * Admin\BookingController
 * Lists all bookings system-wide with status filter. Admin only.
 */
class BookingController extends Controller
{
    /**
     * GET /admin/bookings
     * List all bookings, optionally filtered by payment_status.
     */
    public function index(Request $request)
    {
        $status = $request->query('status'); // optional filter

        $bookings = Booking::with(['user', 'seat.schedule.bus', 'seat.schedule.route'])
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->latest()
            ->paginate(15);

        $statuses = ['pending', 'paid', 'failed', 'cancelled'];

        return view('admin.bookings.index', compact('bookings', 'statuses', 'status'));
    }
}
