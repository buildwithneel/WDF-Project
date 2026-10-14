@extends('layouts.app')

@section('title', 'Select Seat — ' . $schedule->route->source . ' to ' . $schedule->route->destination)

@push('styles')
<style>
    /* ── Seat tile base ───────────────────────────────────────── */
    .seat {
        width: 56px;
        height: 60px;
        border-radius: 10px 10px 6px 6px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
        border: 2px solid transparent;
        transition: all 0.18s ease;
        position: relative;
        user-select: none;
    }
    /* Seat headrest shape */
    .seat::before {
        content: '';
        position: absolute;
        top: -8px;
        left: 8px;
        right: 8px;
        height: 8px;
        background: inherit;
        border-radius: 4px 4px 0 0;
        opacity: 0.7;
    }
    /* ── Status colours ──────────────────────────────────────── */
    .seat-available {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        border-color: #15803d;
        box-shadow: 0 3px 8px rgba(34,197,94,0.35);
    }
    .seat-available:hover {
        transform: translateY(-3px) scale(1.08);
        box-shadow: 0 8px 20px rgba(34,197,94,0.45);
    }
    .seat-held {
        background: linear-gradient(135deg, #eab308, #ca8a04);
        color: white;
        border-color: #a16207;
        cursor: not-allowed;
        box-shadow: 0 3px 8px rgba(234,179,8,0.3);
    }
    .seat-booked {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-color: #b91c1c;
        cursor: not-allowed;
        box-shadow: 0 3px 8px rgba(239,68,68,0.3);
    }
    /* ── Selected state ──────────────────────────────────────── */
    .seat-selected {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        border-color: #1e3a8a !important;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.4), 0 8px 20px rgba(59,130,246,0.4) !important;
        transform: translateY(-3px) scale(1.1) !important;
        color: white !important;
    }
    /* ── Aisle gap ───────────────────────────────────────────── */
    .aisle { width: 36px; }
</style>
@endpush

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-navy">
                {{ $schedule->route->source }} → {{ $schedule->route->destination }}
            </h1>
            <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                <span>🚌 {{ $schedule->bus->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                    @if($schedule->bus->type === 'AC') bg-blue-100 text-blue-700
                    @elseif($schedule->bus->type === 'Sleeper') bg-purple-100 text-purple-700
                    @else bg-green-100 text-green-700 @endif">
                    {{ $schedule->bus->type }}
                </span>
                <span>📅 {{ $schedule->travel_date->format('d M Y') }}</span>
                <span>🕐 {{ \Carbon\Carbon::parse($schedule->departure_time)->format('h:i A') }}</span>
                <span class="font-bold text-navy">💰 ₹{{ number_format($schedule->route->fare, 2) }}</span>
            </div>
        </div>
        <a href="javascript:history.back()"
           class="text-brand hover:underline text-sm font-medium">← Back to Results</a>
    </div>
</div>

{{-- ── Seat Map Card ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Seat Grid (2/3 width) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            {{-- Bus front indicator --}}
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center gap-3 bg-navy text-white text-sm font-semibold px-6 py-2 rounded-full shadow">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.5 2 5 3 5 6v13h14V6c0-3-3.5-4-7-4zm0 2c3 0 5 .75 5 2s-2 2-5 2-5-.75-5-2 2-2 5-2z"/>
                    </svg>
                    DRIVER / FRONT
                </div>
            </div>

            {{-- Column Labels --}}
            <div class="flex items-center justify-center gap-1 mb-3">
                <div style="width:28px"></div>
                <div class="text-xs text-gray-400 font-semibold w-14 text-center">A</div>
                <div class="text-xs text-gray-400 font-semibold w-14 text-center">B</div>
                <div class="aisle"></div>
                <div class="text-xs text-gray-400 font-semibold w-14 text-center">C</div>
                <div class="text-xs text-gray-400 font-semibold w-14 text-center">D</div>
            </div>

            {{-- Booking form wraps the entire seat grid --}}
            {{-- Auth check: only logged-in users can book --}}
            <form id="booking-form" method="POST" action="{{ route('booking.store') }}">
                @csrf {{-- CSRF token for security --}}
                <input type="hidden" name="seat_id" id="selected-seat-id" value="">

                {{-- Render each row of seats (grouped by row letter: A, B, C...) --}}
                @foreach($grouped as $rowLetter => $seats)
                    <div class="flex items-center justify-center gap-1 mb-2">

                        {{-- Row label --}}
                        <div class="w-7 text-xs text-gray-400 font-bold text-right pr-1">
                            {{ $rowLetter }}
                        </div>

                        {{-- Seats 1 and 2 (left of aisle) --}}
                        @foreach($seats->whereIn('seat_no', [$rowLetter.'1', $rowLetter.'2']) as $seat)
                            @include('seats._seat_tile', ['seat' => $seat])
                        @endforeach

                        {{-- Aisle gap --}}
                        <div class="aisle flex items-center justify-center">
                            <div class="w-px h-10 bg-gray-200"></div>
                        </div>

                        {{-- Seats 3 and 4 (right of aisle) --}}
                        @foreach($seats->whereIn('seat_no', [$rowLetter.'3', $rowLetter.'4']) as $seat)
                            @include('seats._seat_tile', ['seat' => $seat])
                        @endforeach
                    </div>
                @endforeach

                {{-- Book Button (hidden until seat selected) --}}
                <div id="book-btn-wrap" class="mt-8 hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-center">
                        <p class="text-blue-700 font-semibold">
                            Seat <span id="selected-label" class="font-extrabold text-blue-900"></span> selected
                        </p>
                        <p class="text-blue-500 text-sm mt-0.5">Your seat will be held for 5 minutes after clicking Book.</p>
                    </div>
                    @auth
                        <button type="submit"
                                id="book-btn"
                                class="w-full bg-brand hover:bg-red-600 text-white font-bold py-4 text-lg rounded-xl shadow-lg shadow-red-100 transition-all transform hover:scale-[1.02]">
                            🎫 Book This Seat — ₹{{ number_format($schedule->route->fare, 2) }}
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-navy hover:bg-gray-800 text-white font-bold py-4 text-lg rounded-xl shadow-lg transition-all">
                            Login to Book →
                        </a>
                    @endauth
                </div>
            </form>
        </div>
    </div>

    {{-- Legend & Stats (1/3 width) --}}
    <div class="space-y-4">

        {{-- Legend --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-navy mb-4">Seat Legend</h3>
            <div class="space-y-3">
                @foreach([
                    ['seat-available', '✓', 'Available', 'Click to select'],
                    ['seat-held',      '⏳', 'Held',      '5-min hold by another user'],
                    ['seat-booked',    '✗', 'Booked',    'Already confirmed'],
                    ['seat-selected',  '●', 'Selected',  'Your selection'],
                ] as [$cls, $icon, $label, $desc])
                    <div class="flex items-center gap-3">
                        <div class="seat {{ $cls }} w-10 h-11 rounded-lg flex-shrink-0 flex items-center justify-center text-white text-sm font-bold shadow-sm" style="position:relative;">
                            {{ $icon }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $label }}</p>
                            <p class="text-xs text-gray-400">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Statistics --}}
        @php
            $all       = $grouped->flatten();
            $available = $all->where('status', 'available')->count();
            $held      = $all->where('status', 'held')->count();
            $booked    = $all->where('status', 'booked')->count();
            $total     = $all->count();
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-navy mb-4">Seat Summary</h3>
            <div class="space-y-3">
                @foreach([
                    [$available, 'Available', 'bg-green-500'],
                    [$held,      'Held',      'bg-yellow-400'],
                    [$booked,    'Booked',    'bg-red-500'],
                ] as [$count, $label, $bar])
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-bold text-gray-800">{{ $count }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            @if($total > 0)
                            <div class="{{ $bar }} h-full rounded-full"
                                 style="width: {{ ($count / $total) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center">Total {{ $total }} seats</p>
                </div>
            </div>
        </div>

        {{-- Hold notice --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
            <p class="text-amber-700 text-sm font-semibold mb-1">⏰ 5-Minute Hold</p>
            <p class="text-amber-600 text-xs">
                When you click "Book", your seat is held for 5 minutes.
                Complete payment before time expires.
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Seat Selection Logic ──────────────────────────────────────
    let selectedSeatId = null;

    document.querySelectorAll('.seat-available').forEach(function(seat) {
        seat.addEventListener('click', function() {
            // Deselect previously selected seat
            document.querySelectorAll('.seat').forEach(s => {
                if (s.classList.contains('seat-selected')) {
                    s.classList.remove('seat-selected');
                }
            });

            // Toggle selection
            if (selectedSeatId === this.dataset.seatId) {
                selectedSeatId = null;
                document.getElementById('selected-seat-id').value = '';
                document.getElementById('book-btn-wrap').classList.add('hidden');
                return;
            }

            // Select this seat
            selectedSeatId = this.dataset.seatId;
            this.classList.add('seat-selected');
            document.getElementById('selected-seat-id').value = selectedSeatId;
            document.getElementById('selected-label').textContent = this.dataset.seatNo;
            document.getElementById('book-btn-wrap').classList.remove('hidden');

            // Scroll to book button on mobile
            document.getElementById('book-btn-wrap').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    // Prevent form submit if no seat selected
    document.getElementById('booking-form').addEventListener('submit', function(e) {
        if (!selectedSeatId) {
            e.preventDefault();
            alert('Please select a seat first.');
        }
    });
</script>
@endpush
