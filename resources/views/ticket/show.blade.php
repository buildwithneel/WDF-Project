@extends('layouts.app')

@section('title', 'E-Ticket — Booking #' . $booking->id)

@push('styles')
<style>
    /* Print: show only the ticket card */
    @media print {
        body { background: white !important; font-size: 12px; }
        .no-print { display: none !important; }
        .print-card {
            box-shadow: none !important;
            border: 2px solid #ddd !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }
    }

    /* Ticket perforation effect */
    .ticket-perf {
        background: radial-gradient(circle at 0 50%, transparent 18px, #f9fafb 18px) left center,
                    radial-gradient(circle at 100% 50%, transparent 18px, #f9fafb 18px) right center;
        background-repeat: no-repeat;
        background-size: 50% 100%;
        height: 24px;
        margin: 0;
    }
</style>
@endpush

@section('content')

@php $schedule = $booking->seat->schedule; @endphp

<div class="max-w-2xl mx-auto">

    {{-- ── Print Button ─────────────────────────────────────────── --}}
    <div class="flex justify-between items-center mb-6 no-print">
        <a href="{{ route('bookings.index') }}" class="text-brand hover:underline text-sm">
            ← My Bookings
        </a>
        <button onclick="window.print()"
                class="bg-navy hover:bg-gray-800 text-white font-semibold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            🖨️ Print / Download
        </button>
    </div>

    {{-- ── E-Ticket Card ────────────────────────────────────────── --}}
    <div class="print-card bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Ticket Header --}}
        <div class="bg-gradient-to-r from-navy to-blue-900 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-2xl">🚌</span>
                        <span class="font-extrabold text-xl">BusGo</span>
                    </div>
                    <p class="text-blue-200 text-sm">Electronic Ticket · Confirmed</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-extrabold">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-xs text-blue-300 mt-0.5">BOOKING ID</div>
                </div>
            </div>

            {{-- Route Banner --}}
            <div class="mt-5 flex items-center justify-center gap-4">
                <div class="text-center">
                    <p class="text-3xl font-black">{{ strtoupper(substr($schedule->route->source, 0, 3)) }}</p>
                    <p class="text-blue-300 text-xs">{{ $schedule->route->source }}</p>
                </div>
                <div class="flex-1 flex items-center gap-1 px-4">
                    <div class="flex-1 h-px bg-blue-400"></div>
                    <span class="text-blue-200">✈</span>
                    <div class="flex-1 h-px bg-blue-400"></div>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-black">{{ strtoupper(substr($schedule->route->destination, 0, 3)) }}</p>
                    <p class="text-blue-300 text-xs">{{ $schedule->route->destination }}</p>
                </div>
            </div>
        </div>

        {{-- Perforation divider --}}
        <div class="h-4 bg-gray-50 relative overflow-hidden">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t-2 border-dashed border-gray-200"></div>
            </div>
        </div>

        {{-- Ticket Body --}}
        <div class="p-6">
            <div class="grid grid-cols-2 gap-x-8 gap-y-5 text-sm">

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Passenger</p>
                    <p class="font-bold text-navy text-base">{{ $booking->user->name }}</p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Seat Number</p>
                    <p class="font-extrabold text-blue-600 text-2xl">{{ $booking->seat->seat_no }}</p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Bus</p>
                    <p class="font-semibold text-gray-800">{{ $schedule->bus->name }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full mt-0.5 inline-block
                        @if($schedule->bus->type === 'AC') bg-blue-100 text-blue-600
                        @elseif($schedule->bus->type === 'Sleeper') bg-purple-100 text-purple-600
                        @else bg-green-100 text-green-600 @endif">
                        {{ $schedule->bus->type }}
                    </span>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Travel Date</p>
                    <p class="font-semibold text-gray-800">{{ $schedule->travel_date->format('d M Y') }}</p>
                    <p class="text-gray-500 text-xs">{{ $schedule->travel_date->format('l') }}</p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Departure</p>
                    <p class="font-bold text-navy text-lg">
                        {{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Fare Paid</p>
                    <p class="font-extrabold text-green-600 text-xl">₹{{ number_format($schedule->route->fare, 2) }}</p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Booked At</p>
                    <p class="font-semibold text-gray-700 text-xs">
                        {{ $booking->booked_at->format('d M Y, h:i A') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Status</p>
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 font-bold px-3 py-1.5 rounded-full text-sm">
                        ✅ CONFIRMED
                    </span>
                </div>
            </div>

            {{-- QR Code Section --}}
            <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col items-center">
                <p class="text-gray-400 text-xs uppercase tracking-wider mb-3">Scan at Bus Stop</p>
                <div class="p-3 bg-white border-2 border-gray-200 rounded-xl shadow-inner">
                    {{-- QR Code using simplesoftwareio/simple-qrcode --}}
                    {!! QrCode::size(160)->generate('BusGo:' . $booking->id . ':' . $booking->seat->seat_no . ':' . $schedule->route->source . '->' . $schedule->route->destination) !!}
                </div>
                <p class="text-gray-400 text-xs mt-2">Booking #{{ $booking->id }}</p>
            </div>
        </div>

        {{-- Ticket Footer --}}
        <div class="bg-gray-50 px-6 py-3 text-center">
            <p class="text-xs text-gray-400">
                This is a simulated ticket for demonstration purposes only.
                Present this ticket at the bus stop.
            </p>
        </div>
    </div>

</div>

@endsection
