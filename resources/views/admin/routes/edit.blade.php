@extends('layouts.app')
@section('title', 'Edit Route')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.routes.index') }}" class="text-brand hover:underline text-sm">← Routes</a>
        <h1 class="text-2xl font-extrabold text-navy">Edit Route</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.routes.update', $route) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Source City</label>
                <input type="text" name="source" value="{{ old('source', $route->source) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('source') border-red-400 @enderror">
                @error('source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Destination City</label>
                <input type="text" name="destination" value="{{ old('destination', $route->destination) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('destination') border-red-400 @enderror">
                @error('destination') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Fare (₹)</label>
                <input type="number" name="fare" value="{{ old('fare', $route->fare) }}" required
                       min="1" step="0.01"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand @error('fare') border-red-400 @enderror">
                @error('fare') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-brand hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                    Update Route
                </button>
                <a href="{{ route('admin.routes.index') }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
