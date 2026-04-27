<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BodaConnect' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#0066CC',
                            secondary: '#FFAD03',
                            tertiary: '#FD9148',
                        },
                    },
                },
            };
        </script>
    @endif
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(ellipse at 10% 20%, rgba(56, 189, 248, 0.08) 0%, transparent 40%),
                        radial-gradient(ellipse at 90% 80%, rgba(124, 58, 237, 0.06) 0%, transparent 40%),
                        linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow-x: hidden;
        }
        .glass-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.75) 0%, rgba(255,255,255,0.5) 100%);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow:
                0px 25px 50px -12px rgba(0, 0, 0, 0.15),
                0px 0px 0px 1px rgba(255, 255, 255, 0.5) inset,
                0px 0px 0px 1px rgba(15, 23, 42, 0.05);
        }
    </style>
</head>
<body class="min-h-screen text-slate-900">
    <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="glass-panel w-full max-w-lg rounded-2xl p-6 sm:p-8">
            <a href="{{ route('home') }}" class="mb-4 flex items-center justify-center">
                <x-logo-mark class="h-16 w-16" />
            </a>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
