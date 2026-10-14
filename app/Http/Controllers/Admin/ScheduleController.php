<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

/**
 * Admin\ScheduleController — Resource Controller
 * Full CRUD for Schedule management. Auto-generates seats on creation.
 * Protected by 'admin' middleware.
 */
class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['bus', 'route'])
            ->withCount('seats')
            ->latest()
            ->paginate(10);
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $buses  = Bus::all();
        $routes = Route::all();
        return view('admin.schedules.create', compact('buses', 'routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_id'         => 'required|exists:buses,id',
            'route_id'       => 'required|exists:routes,id',
            'travel_date'    => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
        ]);

        // Schedule::create triggers the 'created' observer in Schedule model,
        // which auto-generates all seats for this schedule.
        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created and seats generated automatically.');
    }

    public function edit(Schedule $schedule)
    {
        $buses  = Bus::all();
        $routes = Route::all();
        return view('admin.schedules.edit', compact('schedule', 'buses', 'routes'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'bus_id'         => 'required|exists:buses,id',
            'route_id'       => 'required|exists:routes,id',
            'travel_date'    => 'required|date',
            'departure_time' => 'required|date_format:H:i',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        // Cascade delete removes all seats (and their bookings) via FK
        $schedule->delete();
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted.');
    }
}
