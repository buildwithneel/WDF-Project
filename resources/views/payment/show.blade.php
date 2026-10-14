@extends('layouts.app')

@section('title', 'Complete Payment — Booking #' . $booking->id)

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-3xl">💳</span>
        </div>
        <h1 class="text-2xl font-extrabold text-navy">Complete Your Booking</h1>
        <p class="text-gray-500 mt-1">Review your details and confirm payment</p>
    </div>

    {{-- ── Booking Summary Card ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-navy mb-4 border-b border-gray-100 pb-3">
            Booking Summary
        </h2>

        <div class="space-y-3 text-sm">
            @php $schedule = $booking->seat->schedule; @endphp

            <div class="flex justify-between py-2">
                <span class="text-gray-500">Booking ID</span>
                <span class="font-bold text-navy">#{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Passenger</span>
                <span class="font-semibold">{{ auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Route</span>
                <span class="font-semibold">{{ $schedule->route->source }} → {{ $schedule->route->destination }}</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Bus</span>
                <span class="font-semibold">{{ $schedule->bus->name }} ({{ $schedule->bus->type }})</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Travel Date</span>
                <span class="font-semibold">{{ $schedule->travel_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Departure</span>
                <span class="font-semibold">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}</span>
            </div>
            <div class="flex justify-between py-2 border-t border-gray-50">
                <span class="text-gray-500">Seat Number</span>
                <span class="font-bold text-blue-600 text-base">{{ $booking->seat->seat_no }}</span>
            </div>
            <div class="flex justify-between py-3 border-t-2 border-gray-200 mt-2">
                <span class="font-bold text-navy text-base">Total Fare</span>
                <span class="font-extrabold text-brand text-xl">₹{{ number_format($schedule->route->fare, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── 5-Minute Countdown Timer ─────────────────────────────── --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 text-center">
        <p class="text-amber-700 font-semibold mb-2">⏰ Seat Hold Expires In</p>
        <div id="countdown"
             class="text-5xl font-extrabold text-amber-600 tabular-nums tracking-widest"
             data-expires="{{ $booking->seat->held_until->toISOString() }}">
            5:00
        </div>
        <p class="text-amber-500 text-xs mt-2">Complete payment before the timer reaches 0:00</p>
    </div>

    {{-- ── Payment Actions ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Pay Now (simulated) --}}
        <form method="POST" action="{{ route('payment.pay', $booking) }}" id="pay-form">
            @csrf
            <button type="submit"
                    id="pay-btn"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-4 rounded-xl transition-all duration-150 transform hover:scale-[1.02] shadow-lg shadow-green-100 flex items-center justify-center gap-2">
                <span class="text-xl">✅</span>
                <span>Pay Now<br><span class="text-xs font-normal opacity-80">(Simulated)</span></span>
            </button>
        </form>

        {{-- Cancel --}}
        <form method="POST" action="{{ route('payment.cancel', $booking) }}"
              onsubmit="return confirm('Cancel this booking? The seat will be released.')">
            @csrf
            <button type="submit"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-4 rounded-xl transition-all duration-150 flex items-center justify-center gap-2">
                <span class="text-xl">❌</span>
                <span>Cancel<br><span class="text-xs font-normal opacity-60">Release seat</span></span>
            </button>
        </form>
    </div>

    {{-- Demo note --}}
    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-center">
        <p class="text-blue-600 text-xs font-medium">
            🔬 <strong>Demo mode</strong> — No real payment is processed.
            "Pay Now" simulates a successful transaction.
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    /**
     * 5-Minute Countdown Timer
     * Reads the held_until timestamp from the DOM and counts down.
     * When it reaches 0, the Pay button is disabled.
     */
    (function () {
        const countdownEl = document.getElementById('countdown');
        const expiresAt   = new Date(countdownEl.dataset.expires);
        const payBtn      = document.getElementById('pay-btn');

        function updateTimer() {
            const now  = new Date();
            const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));

            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;

            // Format as M:SS
            countdownEl.textContent = minutes + ':' + String(seconds).padStart(2, '0');

            if (diff <= 30) {
                // Turn red when less than 30 seconds remain
                countdownEl.classList.replace('text-amber-600', 'text-red-600');
                countdownEl.classList.add('animate-pulse');
            }

            if (diff === 0) {
                // Time expired — disable pay button and show message
                payBtn.disabled = true;
                payBtn.classList.add('opacity-50', 'cursor-not-allowed');
                payBtn.classList.remove('hover:scale-[1.02]');
                countdownEl.textContent = 'EXPIRED';
                clearInterval(timer);
            }
        }

        // Update every second
        const timer = setInterval(updateTimer, 1000);
        updateTimer(); // Run immediately on load
    })();
</script>
@endpush
