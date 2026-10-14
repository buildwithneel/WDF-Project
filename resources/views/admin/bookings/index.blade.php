@extends('layouts.app')
@section('title', 'Admin Bookings')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">🎫 All Bookings</h1>
        <p class="text-gray-500 text-sm">{{ $bookings->total() }} total bookings</p>
    </div>
</div>

{{-- ── Status Filter ────────────────────────────────────────── --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.bookings.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold transition
              {{ !$status ? 'bg-navy text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
        All
    </a>
    @foreach($statuses as $s)
        <a href="{{ route('admin.bookings.index', ['status' => $s]) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition capitalize
                  {{ $status === $s ? 'bg-navy text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
            {{ ucfirst($s) }}
        </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-4 font-semibold">ID</th>
                <th class="px-6 py-4 font-semibold">Passenger</th>
                <th class="px-6 py-4 font-semibold">Route</th>
                <th class="px-6 py-4 font-semibold">Seat</th>
                <th class="px-6 py-4 font-semibold">Fare</th>
                <th class="px-6 py-4 font-semibold">Date</th>
                <th class="px-6 py-4 font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">#{{ $booking->id }}</td>
                    <td class="px-6 py-4 font-semibold text-navy">{{ $booking->user->name }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ $booking->seat->schedule->route->source }}
                        → {{ $booking->seat->schedule->route->destination }}
                    </td>
                    <td class="px-6 py-4 font-bold text-blue-600">{{ $booking->seat->seat_no }}</td>
                    <td class="px-6 py-4 text-green-600 font-semibold">
                        ₹{{ number_format($booking->seat->schedule->route->fare, 2) }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $booking->seat->schedule->travel_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            @if($booking->payment_status === 'paid')      bg-green-100 text-green-700
                            @elseif($booking->payment_status === 'pending')   bg-yellow-100 text-yellow-700
                            @elseif($booking->payment_status === 'cancelled') bg-gray-100 text-gray-500
                            @else bg-red-100 text-red-600 @endif">
                            {{ strtoupper($booking->payment_status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">No bookings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $bookings->links() }}</div>

@endsection
