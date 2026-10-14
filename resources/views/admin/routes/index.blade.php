@extends('layouts.app')
@section('title', 'Manage Routes')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">🗺️ Routes</h1>
        <p class="text-gray-500 text-sm">{{ $routes->total() }} total routes</p>
    </div>
    <a href="{{ route('admin.routes.create') }}"
       class="bg-brand text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-red-600 transition">
        + Add Route
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-4 font-semibold">ID</th>
                <th class="px-6 py-4 font-semibold">Source</th>
                <th class="px-6 py-4 font-semibold">Destination</th>
                <th class="px-6 py-4 font-semibold">Fare</th>
                <th class="px-6 py-4 font-semibold">Schedules</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($routes as $route)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">#{{ $route->id }}</td>
                    <td class="px-6 py-4 font-semibold text-navy">{{ $route->source }}</td>
                    <td class="px-6 py-4 font-semibold text-navy">{{ $route->destination }}</td>
                    <td class="px-6 py-4 text-green-600 font-bold">₹{{ number_format($route->fare, 2) }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $route->schedules_count }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.routes.edit', $route) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.routes.destroy', $route) }}"
                                  onsubmit="return confirm('Delete this route?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 font-medium text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">No routes yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $routes->links() }}</div>

@endsection
