@extends('layouts.auth')

@section('title', 'Sign In — BusGo')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-navy">Welcome back</h1>
    <p class="text-sm text-gray-500 mt-1">Sign in to your BusGo account to continue</p>
</div>

{{-- Session Status --}}
@if (session('status'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
        {{ session('status') }}
    </div>
@endif

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

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password"
               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand focus:border-transparent text-sm transition">
    </div>

    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand focus:ring-brand">
            <span class="text-gray-600">Remember me</span>
        </label>
    </div>

    <button type="submit"
            class="w-full bg-brand hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition">
        Sign In
    </button>
</form>

{{-- Demo Credentials Quick-Fill for University Presentation --}}
<div class="mt-6 pt-5 border-t border-gray-100">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Demo Credentials (Click to fill)</p>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <button type="button" onclick="fillCreds('admin@busgo.com', 'password123')"
                class="p-2 border border-gray-200 rounded-lg hover:border-brand hover:bg-red-50 text-left transition">
            <div class="font-bold text-navy">👑 Admin Demo</div>
            <div class="text-gray-500 text-[11px] truncate">admin@busgo.com</div>
        </button>
        <button type="button" onclick="fillCreds('user@busgo.com', 'password123')"
                class="p-2 border border-gray-200 rounded-lg hover:border-brand hover:bg-red-50 text-left transition">
            <div class="font-bold text-navy">👤 User Demo</div>
            <div class="text-gray-500 text-[11px] truncate">user@busgo.com</div>
        </button>
    </div>
</div>

<p class="text-center text-sm text-gray-500 mt-6">
    Don't have an account?
    <a href="{{ route('register') }}" class="font-semibold text-brand hover:underline">Sign up</a>
</p>

<script>
    function fillCreds(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
