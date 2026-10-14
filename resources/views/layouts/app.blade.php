<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Bus Seat Booking System - Book your bus tickets online easily">
    <title>@yield('title', 'Smart Bus Booking') | BusGo</title>

    {{-- Google Fonts: Inter for clean modern typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS via CDN (fallback if npm build not run) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:  '#1F2A44',
                        brand: '#FF2D20',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Custom styles for print and animations --}}
    <style>
        /* ── Base ────────────────────────────────────────────── */
        body { font-family: 'Inter', sans-serif; background: #F3F4F6; }

        /* ── Navbar gradient ─────────────────────────────────── */
        .navbar { background: linear-gradient(135deg, #1F2A44 0%, #2d3f63 100%); }

        /* ── Flash message animations ────────────────────────── */
        .flash-msg { animation: slideIn 0.4s ease-out; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Seat tile hover effect ───────────────────────────── */
        .seat-tile { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .seat-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.18); }

        /* ── Print stylesheet ─────────────────────────────────── */
        @media print {
            nav, .no-print, footer { display: none !important; }
            body { background: white !important; }
            .print-card { box-shadow: none !important; border: 2px solid #ddd; }
        }
    </style>

    @stack('styles')
</head>
<body class="h-full flex flex-col">

    {{-- ── Navigation Bar ──────────────────────────────────────── --}}
    <nav class="navbar shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Brand Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-brand rounded-lg flex items-center justify-center shadow">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl tracking-tight">BusGo</span>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}"
                       class="text-gray-300 hover:text-white hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                        🔍 Search
                    </a>

                    @auth
                        <a href="{{ route('bookings.index') }}"
                           class="text-gray-300 hover:text-white hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                            🎫 My Bookings
                        </a>

                        {{-- Admin link: only visible to admin users --}}
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-yellow-300 hover:text-yellow-100 hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                                ⚙️ Admin
                            </a>
                        @endif

                        {{-- User dropdown --}}
                        <span class="text-gray-400 text-sm px-2">{{ auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="bg-brand hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-gray-300 hover:text-white hover:bg-white/10 px-3 py-2 rounded-lg text-sm font-medium transition-all">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-brand hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ── Flash Messages ───────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto w-full px-4 pt-4">
        @foreach(['success' => 'green', 'error' => 'red', 'info' => 'blue', 'warning' => 'yellow'] as $type => $color)
            @if(session($type))
                <div class="flash-msg mb-4 p-4 rounded-xl border-l-4 border-{{ $color }}-500 bg-{{ $color }}-50 text-{{ $color }}-800 flex items-start gap-3 shadow-sm">
                    <span class="text-lg">
                        @if($type === 'success') ✅
                        @elseif($type === 'error') ❌
                        @elseif($type === 'info') ℹ️
                        @else ⚠️
                        @endif
                    </span>
                    <span class="font-medium">{{ session($type) }}</span>
                </div>
            @endif
        @endforeach
    </div>

    {{-- ── Main Content ─────────────────────────────────────────── --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 py-6">
        @yield('content')
    </main>

    {{-- ── Footer ───────────────────────────────────────────────── --}}
    <footer class="navbar mt-auto py-6 no-print">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
            <p>🚌 <strong class="text-white">BusGo</strong> — Smart Bus Seat Booking System</p>
            <p class="mt-1">Built with <span class="text-brand font-semibold">Laravel 11</span> · PHP 8.2 · MySQL · Blade · Tailwind CSS</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
