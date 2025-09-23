<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex items-center justify-center min-h-screen font-sans p-6">

    <div class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-12 relative z-10 border border-gray-200 dark:border-gray-800 transition-all duration-300 transform hover:scale-[1.01]">

        <div class="text-center mb-8">
            {{-- Logo central --}}
            <img src="{{ asset('assets/imgs/pikhn3.png') }}" alt="Logo" class="mx-auto h-24 mb-4 transform transition-transform hover:scale-105 duration-300">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Bienvenido</h2>
            <p class="text-base text-gray-500 dark:text-gray-400">Accede a tu cuenta para continuar.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" class="text-gray-700 dark:text-gray-300 mb-1 text-base" />
                <x-text-input id="email" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 py-3 text-base" 
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
            </div>

            <div class="relative">
                <x-input-label for="password" :value="__('Contraseña')" class="text-gray-700 dark:text-gray-300 mb-1 text-base" />
                <x-text-input id="password" 
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 py-3 text-base pr-10"
                    type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                <span class="absolute right-3 top-10 cursor-pointer text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" 
                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 transition-colors duration-200" 
                        name="remember">
                    <label for="remember_me" class="ml-2 text-base text-gray-600 dark:text-gray-400">
                        {{ __('Recordarme') }}
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a class="text-base text-blue-600 dark:text-blue-400 hover:underline hover:text-blue-700 dark:hover:text-blue-500 transition-colors duration-200" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <button type="submit"
                class="w-full px-6 py-3 bg-blue-600 text-white rounded-md font-semibold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105 text-base">
                {{ __('Iniciar Sesión') }}
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-base text-gray-600 dark:text-gray-400">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline hover:text-blue-700 dark:hover:text-blue-500 transition-colors duration-200">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Alternar el tipo de entrada
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // Alternar el ícono del ojo
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>