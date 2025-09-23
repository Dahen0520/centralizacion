<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bienvenido a tu App</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
        <script src="https://cdn.tailwindcss.com"></script>
        
    </head>
    <body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col font-sans">

        <header class="w-full text-base mb-8 absolute top-0 left-0 p-6 lg:p-8 z-10">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    {{-- Botón para registrar un afiliado --}}
                    @guest
                        <a
                            href="{{ route('afiliados.registro') }}"
                            class="inline-block px-6 py-2 text-blue-300 border border-blue-400 rounded-md text-sm font-medium leading-normal hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale"
                        >
                            Registrar Empresa
                        </a>
                    @endguest
                    
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-6 py-2 bg-blue-600 text-white dark:bg-blue-700 dark:hover:bg-blue-800 rounded-md text-sm font-medium leading-normal shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale"
                        >
                            Ir al Panel
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-6 py-2 text-blue-300 border border-blue-400 rounded-md text-sm font-medium leading-normal hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale"
                        >
                            Iniciar Sesión
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="inline-block px-6 py-2 text-blue-300 border border-blue-400 rounded-md text-sm font-medium leading-normal hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale"
                        >
                            Registrar
                        </a>
                        
                    @endauth
                </nav>
            @endif
        </header>

        <main class="text-center">
            {{-- Logo grande central en lugar del texto de bienvenida --}}
            <img src="assets/imgs/pikhn3.png" alt="Logo de Cooperativa Chorotega" class="welcome-main-logo">

            @if (!Route::has('login'))
                <a
                    href="#"
                    class="inline-block px-8 py-3 bg-blue-600 text-white dark:bg-blue-700 dark:hover:bg-blue-800 rounded-lg text-lg font-bold shadow-xl hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 btn-hover-scale"
                >
                    Explorar Ahora
                </a>
            @endif
        </main>
    </body>
</html>