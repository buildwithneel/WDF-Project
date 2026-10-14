@extends('layouts.app')
@section('title', 'Add Schedule')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.schedules.index') }}" class="text-brand hover:underline text-sm">← Schedules</a>
        <h1 class="text-2xl font-extrabold text-navy">Add New Schedule</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-blue-700 text-sm font-medium">
                💡 Seats will be <strong>automatically generated</strong> when you create a schedule,
                based on the bus's total seat count.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus</label>
                <select name="bus_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('bus_id') border-red-400 @enderror">
                    <option value="">Select bus</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                            {{ $bus->name }} ({{ $bus->type }}, {{ $bus->total_seats }} seats)
                        </option>
                    @endforeach
                </select>
                @error('bus_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Route</label>
                <select name="route_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('route_id') border-red-400 @enderror">
                    <option value="">Select route</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                            {{ $route->source }} → {{ $route->destination }} (₹{{ $route->fare }})
                        </option>
                    @endforeach
                </select>
                @error('route_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Travel Date</label>
                <input type="date" name="travel_date" value="{{ old('travel_date') }}"
                       min="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('travel_date') border-red-400 @enderror">
                @error('travel_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Departure Time</label>
                <input type="time" name="departure_time" value="{{ old('departure_time', '08:00') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('departure_time') border-red-400 @enderror">
                @error('departure_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="w-full bg-brand hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                Create Schedule (Seats Auto-Generated)
            </button>
        </form>
    </div>
</div>

@endsection
