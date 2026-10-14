@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-extrabold text-navy">🎫 My Bookings</h1>
    <a href="{{ route('home') }}"
       class="bg-brand text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-600 transition">
        + New Booking
    </a>
</div>

@if($bookings->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <div class="text-6xl mb-4">🎫</div>
        <h2 class="text-xl font-bold text-navy mb-2">No Bookings Yet</h2>
        <p class="text-gray-500 mb-6">You haven't booked any trips yet. Start your journey!</p>
        <a href="{{ route('home') }}"
           class="bg-brand text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            Search Buses
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($bookings as $booking)
            @php
                $seat     = $booking->seat;
                $schedule = $seat->schedule;
                $isPast   = $schedule->travel_date->isPast();
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                    {{-- Booking Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="font-bold text-navy text-lg">
                                {{ $schedule->route->source }} → {{ $schedule->route->destination }}
                            </span>

                            {{-- Status Badge --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                @if($booking->payment_status === 'paid')     bg-green-100 text-green-700
                                @elseif($booking->payment_status === 'pending')  bg-yellow-100 text-yellow-700
                                @elseif($booking->payment_status === 'cancelled') bg-gray-100 text-gray-500
                                @else bg-red-100 text-red-600 @endif">
                                @if($booking->payment_status === 'paid') ✅ CONFIRMED
                                @elseif($booking->payment_status === 'pending') ⏳ PENDING
                                @elseif($booking->payment_status === 'cancelled') ✕ CANCELLED
                                @else ✗ FAILED @endif
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                            <span>🚌 {{ $schedule->bus->name }}</span>
                            <span>💺 Seat <strong class="text-gray-700">{{ $seat->seat_no }}</strong></span>
                            <span>📅 {{ $schedule->travel_date->format('d M Y') }}</span>
                            <span>🕐 {{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}</span>
                            <span>💰 ₹{{ number_format($schedule->route->fare, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Booking #{{ $booking->id }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-2 items-end">
                        @if($booking->payment_status === 'paid')
                            <a href="{{ route('ticket.show', $booking) }}"
                               class="bg-navy text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-800 transition">
                                View Ticket
                            </a>

                            {{-- Cancel only allowed for future paid bookings --}}
                            @if(!$isPast)
                                <form method="POST"
                                      action="{{ route('bookings.cancel', $booking) }}"
                                      onsubmit="return confirm('Cancel this booking? This cannot be undone.')">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:text-red-700 hover:underline transition">
                                        Cancel Booking
                                    </button>
                                </form>
                            @endif

                        @elseif($booking->payment_status === 'pending')
                            <a href="{{ route('payment.show', $booking) }}"
                               class="bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-amber-600 transition">
                                Complete Payment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
@endif

@endsection
