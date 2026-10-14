@extends('layouts.app')
@section('title', 'Add Bus')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.buses.index') }}" class="text-brand hover:underline text-sm">← Buses</a>
        <h1 class="text-2xl font-extrabold text-navy">Add New Bus</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.buses.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Gujarat Express"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bus Type</label>
                <select name="type" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('type') border-red-400 @enderror">
                    <option value="">Select type</option>
                    @foreach(['AC', 'Non-AC', 'Sleeper'] as $type)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Total Seats <span class="text-gray-400 font-normal">(must be multiple of 4)</span>
                </label>
                <input type="number" name="total_seats" value="{{ old('total_seats', 40) }}"
                       min="4" max="60" step="4" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('total_seats') border-red-400 @enderror">
                @error('total_seats') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="w-full bg-brand hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                Create Bus
            </button>
        </form>
    </div>
</div>

@endsection
