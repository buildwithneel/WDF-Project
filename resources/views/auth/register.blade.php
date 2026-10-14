@extends('layouts.auth')

@section('title', 'Sign Up — BusGo')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-navy">Create an account</h1>
    <p class="text-sm text-gray-500 mt-1">Book your bus seats quickly and securely</p>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
               placeholder="Jane Doe"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
               placeholder="jane@example.com"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               placeholder="••••••••"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               placeholder="••••••••"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <button type="submit"
            class="w-full bg-brand hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition">
        Create Account
    </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Already have an account?
    <a href="{{ route('login') }}" class="font-semibold text-brand hover:underline">Sign in</a>
</p>
@endsection
