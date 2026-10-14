@extends('layouts.app')
@section('title', 'Edit Schedule')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.schedules.index') }}" class="text-brand hover:underline text-sm">← Schedules</a>
        <h1 class="text-2xl font-extrabold text-navy">Edit Schedule</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus</label>
                <select name="bus_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" {{ old('bus_id', $schedule->bus_id) == $bus->id ? 'selected' : '' }}>
                            {{ $bus->name }} ({{ $bus->type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Route</label>
                <select name="route_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->source }} → {{ $route->destination }} (₹{{ $route->fare }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Travel Date</label>
                <input type="date" name="travel_date"
                       value="{{ old('travel_date', $schedule->travel_date->format('Y-m-d')) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Departure Time</label>
                <input type="time" name="departure_time"
                       value="{{ old('departure_time', substr($schedule->departure_time, 0, 5)) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-brand hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                    Update Schedule
                </button>
                <a href="{{ route('admin.schedules.index') }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
