@extends('layouts.app')

@section('title', 'Search Results — ' . $validated['source'] . ' to ' . $validated['destination'])

@section('content')

{{-- ── Header ──────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">
            {{ $validated['source'] }} → {{ $validated['destination'] }}
        </h1>
        <p class="text-gray-500 text-sm mt-0.5">
            {{ \Carbon\Carbon::parse($validated['date'])->format('l, d M Y') }}
            · {{ $schedules->count() }} bus(es) found
        </p>
    </div>
    <a href="{{ route('home') }}"
       class="text-sm text-brand hover:underline font-medium">← Modify Search</a>
</div>

{{-- ── Results List ─────────────────────────────────────────── --}}
@forelse($schedules as $schedule)
    @php
        $available = $schedule->seats->where('status', 'available')->count();
        $total     = $schedule->seats->count();
    @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4 hover:shadow-md transition-all duration-200 hover:border-brand/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

            {{-- Bus Info --}}
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow
                        @if($schedule->bus->type === 'AC') bg-blue-500
                        @elseif($schedule->bus->type === 'Sleeper') bg-purple-500
                        @else bg-green-500 @endif">
                        {{ strtoupper(substr($schedule->bus->type, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-navy text-lg">{{ $schedule->bus->name }}</p>
                        <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium
                            @if($schedule->bus->type === 'AC') bg-blue-100 text-blue-700
                            @elseif($schedule->bus->type === 'Sleeper') bg-purple-100 text-purple-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ $schedule->bus->type }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    <span>🕐 Departs <strong>{{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}</strong></span>
                    <span>💺 <strong>{{ $available }}</strong> / {{ $total }} seats available</span>
                </div>
            </div>

            {{-- Fare & CTA --}}
            <div class="text-right flex flex-col items-end gap-3">
                <div>
                    <p class="text-3xl font-extrabold text-navy">₹{{ number_format($schedule->route->fare, 2) }}</p>
                    <p class="text-xs text-gray-400">per person</p>
                </div>

                @if($available > 0)
                    <a href="{{ route('seats.show', $schedule) }}"
                       class="bg-brand hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-150 transform hover:scale-105 shadow-md shadow-red-100">
                        View Seats →
                    </a>
                @else
                    <span class="bg-gray-100 text-gray-400 font-semibold px-6 py-3 rounded-xl cursor-not-allowed">
                        Sold Out
                    </span>
                @endif
            </div>
        </div>

        {{-- Seat availability bar --}}
        <div class="mt-4 pt-4 border-t border-gray-50">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Availability</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all
                        @if($available/$total > 0.5) bg-green-400
                        @elseif($available/$total > 0.2) bg-yellow-400
                        @else bg-red-400 @endif"
                         style="width: {{ $total > 0 ? ($available / $total) * 100 : 0 }}%">
                    </div>
                </div>
                <span class="text-xs font-medium text-gray-600">{{ $available }} left</span>
            </div>
        </div>
    </div>

@empty
    {{-- No results state --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <div class="text-6xl mb-4">🚌</div>
        <h2 class="text-xl font-bold text-navy mb-2">No buses found</h2>
        <p class="text-gray-500 mb-6">
            No schedules available for
            <strong>{{ $validated['source'] }} → {{ $validated['destination'] }}</strong>
            on {{ \Carbon\Carbon::parse($validated['date'])->format('d M Y') }}.
        </p>
        <a href="{{ route('home') }}"
           class="bg-brand text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            Try Different Date
        </a>
    </div>
@endforelse

@endsection
