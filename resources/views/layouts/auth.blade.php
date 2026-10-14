<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BusGo Auth')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { navy: '#1F2A44', brand: '#FF2D20' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center">
                    <span class="text-white text-lg">🚌</span>
                </div>
                <span class="text-2xl font-extrabold text-navy">BusGo</span>
            </a>
        </div>

        {{-- Content Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
