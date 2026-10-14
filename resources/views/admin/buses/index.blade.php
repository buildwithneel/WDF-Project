{{-- Shared admin list layout partial --}}
@extends('layouts.app')

@section('title', 'Manage Buses')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-navy">🚌 Buses</h1>
        <p class="text-gray-500 text-sm">{{ $buses->total() }} total buses</p>
    </div>
    <a href="{{ route('admin.buses.create') }}"
       class="bg-brand text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-red-600 transition">
        + Add Bus
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                <th class="px-6 py-4 font-semibold">ID</th>
                <th class="px-6 py-4 font-semibold">Name</th>
                <th class="px-6 py-4 font-semibold">Type</th>
                <th class="px-6 py-4 font-semibold">Seats</th>
                <th class="px-6 py-4 font-semibold">Schedules</th>
                <th class="px-6 py-4 font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($buses as $bus)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">#{{ $bus->id }}</td>
                    <td class="px-6 py-4 font-semibold text-navy">{{ $bus->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold
                            @if($bus->type === 'AC') bg-blue-100 text-blue-700
                            @elseif($bus->type === 'Sleeper') bg-purple-100 text-purple-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ $bus->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $bus->total_seats }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $bus->schedules_count }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.buses.edit', $bus) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.buses.destroy', $bus) }}"
                                  onsubmit="return confirm('Delete this bus? All its schedules and seats will be deleted.')">
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">No buses yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $buses->links() }}</div>

@endsection
