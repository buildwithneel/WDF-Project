@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">⚙️ Admin Dashboard</h1>
        <p class="text-gray-500 text-sm mt-0.5">System overview and statistics</p>
    </div>
    <span class="bg-yellow-100 text-yellow-700 text-sm font-bold px-3 py-1.5 rounded-full">
        👑 Admin Panel
    </span>
</div>

{{-- ── Stats Cards ──────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['🚌', 'Buses',     $stats['buses'],     'bg-blue-50 text-blue-600',   'Total registered buses'],
        ['🗺️', 'Routes',    $stats['routes'],    'bg-green-50 text-green-600', 'Active routes'],
        ['📅', 'Schedules', $stats['schedules'], 'bg-purple-50 text-purple-600','Scheduled trips'],
        ['🎫', 'Bookings',  $stats['bookings'],  'bg-orange-50 text-orange-600','All time bookings'],
        ['💰', 'Revenue',   '₹' . number_format($stats['revenue'], 0), 'bg-red-50 text-brand', 'From paid bookings'],
    ] as [$icon, $label, $value, $cls, $hint])
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all">
            <div class="w-10 h-10 {{ $cls }} rounded-xl flex items-center justify-center text-xl mb-3">
                {{ $icon }}
            </div>
            <p class="text-2xl font-extrabold text-navy">{{ $value }}</p>
            <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $label }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $hint }}</p>
        </div>
    @endforeach
</div>

{{-- ── Quick Nav ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        [route('admin.buses.index'),     '🚌', 'Manage Buses',     'Create, edit, delete buses'],
        [route('admin.routes.index'),    '🗺️', 'Manage Routes',    'Set sources, destinations, fares'],
        [route('admin.schedules.index'), '📅', 'Manage Schedules', 'Assign buses to routes'],
        [route('admin.bookings.index'),  '🎫', 'View Bookings',    'All bookings with filter'],
    ] as [$url, $icon, $title, $desc])
        <a href="{{ $url }}"
           class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-brand/30 transition-all group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform inline-block">{{ $icon }}</div>
            <p class="font-bold text-navy text-sm">{{ $title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $desc }}</p>
        </a>
    @endforeach
</div>

{{-- ── Recent Bookings ──────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-navy">Recent Bookings</h2>
        <a href="{{ route('admin.bookings.index') }}"
           class="text-sm text-brand hover:underline font-medium">View All →</a>
    </div>

    @if($recentBookings->isEmpty())
        <p class="text-gray-400 text-center py-6">No bookings yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 font-semibold">#</th>
                        <th class="pb-3 font-semibold">Passenger</th>
                        <th class="pb-3 font-semibold">Route</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentBookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 text-gray-400">#{{ $booking->id }}</td>
                            <td class="py-3 font-medium text-navy">{{ $booking->user->name }}</td>
                            <td class="py-3 text-gray-600">
                                {{ $booking->seat->schedule->route->source }}
                                → {{ $booking->seat->schedule->route->destination }}
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    @if($booking->payment_status === 'paid')      bg-green-100 text-green-700
                                    @elseif($booking->payment_status === 'pending')   bg-yellow-100 text-yellow-700
                                    @elseif($booking->payment_status === 'cancelled') bg-gray-100 text-gray-500
                                    @else bg-red-100 text-red-600 @endif">
                                    {{ strtoupper($booking->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
