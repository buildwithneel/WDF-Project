<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

/**
 * SearchController
 *
 * Handles the home page and schedule search functionality.
 * MVC Flow: routes/web.php → SearchController → search/index.blade.php
 */
class SearchController extends Controller
{
    /**
     * Show the home/search page.
     * GET /
     *
     * Fetches distinct sources and destinations for the search form dropdowns.
     */
    public function index()
    {
        // Get unique source cities for the dropdown
        $sources      = Route::distinct()->pluck('source')->sort()->values();
        $destinations = Route::distinct()->pluck('destination')->sort()->values();

        return view('search.index', compact('sources', 'destinations'));
    }

    /**
     * Show schedules matching the search criteria.
     * GET /search?source=...&destination=...&date=...
     *
     * Validates input, queries matching schedules, eager-loads relationships
     * to avoid N+1 queries (with() = one JOIN instead of many queries).
     */
    public function search(Request $request)
    {
        // Validate the search form inputs
        $validated = $request->validate([
            'source'      => 'required|string',
            'destination' => 'required|string',
            'date'        => 'required|date|after_or_equal:today',
        ]);

        // Eloquent query: find matching schedules with eager loading
        // join routes table via the route relationship to filter source/destination
        $schedules = Schedule::with(['bus', 'route'])  // eager-load to avoid N+1
            ->whereHas('route', function ($q) use ($validated) {
                $q->where('source', $validated['source'])
                  ->where('destination', $validated['destination']);
            })
            ->where('travel_date', $validated['date'])
            ->orderBy('departure_time')
            ->get();

        return view('search.results', compact('schedules', 'validated'));
    }
}
