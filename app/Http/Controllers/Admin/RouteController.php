<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

/**
 * Admin\RouteController — Resource Controller
 * Full CRUD for Route management. Protected by 'admin' middleware.
 */
class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::withCount('schedules')->latest()->paginate(10);
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source'      => 'required|string|max:100',
            'destination' => 'required|string|max:100|different:source',
            'fare'        => 'required|numeric|min:1|max:99999.99',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully.');
    }

    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'source'      => 'required|string|max:100',
            'destination' => 'required|string|max:100|different:source',
            'fare'        => 'required|numeric|min:1|max:99999.99',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')
            ->with('success', 'Route deleted.');
    }
}
