@extends('layouts.app')
@section('title', 'Edit Bus — ' . $bus->name)
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.buses.index') }}" class="text-brand hover:underline text-sm">← Buses</a>
        <h1 class="text-2xl font-extrabold text-navy">Edit Bus</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.buses.update', $bus) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus Name</label>
                <input type="text" name="name" value="{{ old('name', $bus->name) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus Type</label>
                <select name="type" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                    @foreach(['AC', 'Non-AC', 'Sleeper'] as $type)
                        <option value="{{ $type }}" {{ old('type', $bus->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Total Seats</label>
                <input type="number" name="total_seats" value="{{ old('total_seats', $bus->total_seats) }}"
                       min="4" max="60" step="4" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('total_seats') border-red-400 @enderror">
                @error('total_seats') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-brand hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                    Update Bus
                </button>
                <a href="{{ route('admin.buses.index') }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
