<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reservas Chorotega') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chorotega-blue': '#2c3991',
                        'chorotega-yellow': '#f5b800',
                        'bg-light-gray': '#edf0fa',
                        'chorotega-blue-light': '#3a4aab',
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-bg-light-gray min-h-screen flex">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    @include('partials.sidebar')

    <div class="flex-1 ml-56 flex flex-col relative">
        <div class="sticky top-0 z-40 bg-white shadow">
            @include('partials.top-header')
        </div>

        <main class="p-6 flex-1 overflow-y-auto flex flex-col justify-between">
            <div id="contenido" class="mb-8">
                @isset($header)
                    <h2 class="text-2xl font-bold text-chorotega-blue mb-4 text-center">{{ $header }}</h2>
                @else
                    <h2 class="text-2xl font-bold text-chorotega-blue mb-4 text-center">Bienvenido al sistema</h2>
                @endisset

                {{ $slot }}
            </div>
            <footer class="text-sm text-center text-gray-500 border-t pt-4">
                <p>&copy; <span id="year"></span> Cooperativa Chorotega. Todos los derechos reservados. devDahen</p>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const yearSpan = document.getElementById("year");
            if (yearSpan) yearSpan.textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>