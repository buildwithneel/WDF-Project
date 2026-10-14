@extends('layouts.app')
@section('title', 'Manage Schedules')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">📅 Schedules</h1>
        <p class="text-gray-500 text-sm">{{ $schedules->total() }} total schedules</p>
    </div>
    <a href="{{ route('admin.schedules.create') }}"
       class="bg-brand text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-red-600 transition">
        + Add Schedule
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-4 font-semibold">ID</th>
                <th class="px-6 py-4 font-semibold">Bus</th>
                <th class="px-6 py-4 font-semibold">Route</th>
                <th class="px-6 py-4 font-semibold">Date</th>
                <th class="px-6 py-4 font-semibold">Departure</th>
                <th class="px-6 py-4 font-semibold">Seats</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">#{{ $schedule->id }}</td>
                    <td class="px-6 py-4 font-semibold text-navy">{{ $schedule->bus->name }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ $schedule->route->source }} → {{ $schedule->route->destination }}
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $schedule->travel_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $schedule->seats_count }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('seats.show', $schedule) }}"
                               class="text-green-600 hover:text-green-800 font-medium text-xs">View Seats</a>
                            <a href="{{ route('admin.schedules.edit', $schedule) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                                  onsubmit="return confirm('Delete this schedule and all its seats/bookings?')">
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">No schedules yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $schedules->links() }}</div>

@endsection
