@extends('layouts.app')

@section('title', 'Search Buses')

@section('content')

{{-- ── Hero Section ──────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-3xl mb-8"
     style="background: linear-gradient(135deg, #1F2A44 0%, #2d3f63 50%, #FF2D20 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-8 left-8 w-32 h-32 bg-white rounded-full"></div>
        <div class="absolute bottom-4 right-16 w-20 h-20 bg-white rounded-full"></div>
        <div class="absolute top-16 right-32 w-12 h-12 bg-white rounded-full"></div>
    </div>
    <div class="relative px-8 py-14 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-3 tracking-tight">
            🚌 Book Your Bus Seat
        </h1>
        <p class="text-lg text-blue-100 max-w-xl mx-auto">
            Fast, easy, and secure bus ticket booking across Gujarat & Maharashtra.
            Reserve your seat in under 2 minutes.
        </p>
    </div>
</div>

{{-- ── Search Form Card ─────────────────────────────────────── --}}
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <h2 class="text-xl font-bold text-navy mb-6 flex items-center gap-2">
            <span class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-white text-sm">🔍</span>
            Find Buses
        </h2>

        {{-- Search form — GET /search --}}
        <form action="{{ route('search') }}" method="GET" class="space-y-5">

            {{-- Source --}}
            <div>
                <label for="source" class="block text-sm font-semibold text-gray-700 mb-1">
                    From (Source)
                </label>
                <select id="source" name="source" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent bg-gray-50 transition">
                    <option value="">-- Select departure city --</option>
                    @foreach($sources as $src)
                        <option value="{{ $src }}" {{ old('source') == $src ? 'selected' : '' }}>
                            {{ $src }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Destination --}}
            <div>
                <label for="destination" class="block text-sm font-semibold text-gray-700 mb-1">
                    To (Destination)
                </label>
                <select id="destination" name="destination" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent bg-gray-50 transition">
                    <option value="">-- Select arrival city --</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest }}" {{ old('destination') == $dest ? 'selected' : '' }}>
                            {{ $dest }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Travel Date --}}
            <div>
                <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">
                    Travel Date
                </label>
                <input type="date" id="date" name="date" required
                       min="{{ date('Y-m-d') }}"
                       value="{{ old('date', date('Y-m-d')) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent bg-gray-50 transition">
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                    class="w-full bg-brand hover:bg-red-600 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg shadow-red-200 text-lg">
                Search Available Buses →
            </button>
        </form>
    </div>
</div>

{{-- ── Feature Highlights ───────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
    @foreach([
        ['🔒', 'Secure Booking', 'Your seat is held for 5 minutes while you complete payment.'],
        ['⚡', 'Instant Confirmation', 'Get your e-ticket instantly with QR code after payment.'],
        ['🎯', 'Visual Seat Map', 'Choose your preferred seat from an interactive 2+2 layout.'],
    ] as [$icon, $title, $desc])
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-3xl mb-3">{{ $icon }}</div>
            <h3 class="font-bold text-navy text-base mb-1">{{ $title }}</h3>
            <p class="text-gray-500 text-sm">{{ $desc }}</p>
        </div>
    @endforeach
</div>

@endsection
