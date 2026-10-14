<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;

/**
 * Admin\BusController — Resource Controller
 *
 * Full CRUD for Bus management.
 * Laravel resource controllers map 7 standard actions to routes automatically.
 * Protected by 'admin' middleware.
 */
class BusController extends Controller
{
    /** GET /admin/buses — list all buses */
    public function index()
    {
        $buses = Bus::withCount('schedules')->latest()->paginate(10);
        return view('admin.buses.index', compact('buses'));
    }

    /** GET /admin/buses/create — show create form */
    public function create()
    {
        return view('admin.buses.create');
    }

    /** POST /admin/buses — store new bus */
    public function store(Request $request)
    {
        // Validate all inputs; Eloquent uses these validated values safely
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:AC,Non-AC,Sleeper',
            'total_seats' => 'required|integer|min:4|max:60|multiple_of:4',
        ]);

        Bus::create($validated);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus created successfully.');
    }

    /** GET /admin/buses/{bus}/edit — show edit form */
    public function edit(Bus $bus)
    {
        return view('admin.buses.edit', compact('bus'));
    }

    /** PUT /admin/buses/{bus} — update bus */
    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:AC,Non-AC,Sleeper',
            'total_seats' => 'required|integer|min:4|max:60|multiple_of:4',
        ]);

        $bus->update($validated);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus updated successfully.');
    }

    /** DELETE /admin/buses/{bus} — delete bus */
    public function destroy(Bus $bus)
    {
        $bus->delete(); // Cascade deletes schedules and seats via FK constraint
        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus deleted.');
    }
}
