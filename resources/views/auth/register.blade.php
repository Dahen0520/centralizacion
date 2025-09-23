<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarse</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex items-center justify-center min-h-screen font-sans p-6">

    <div class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-12 relative z-10 border border-gray-200 dark:border-gray-800 transition-all duration-300 transform hover:scale-[1.01]">

        <div class="text-center mb-8">
            {{-- Logo central --}}
            <img src="{{ asset('assets/imgs/pikhn3.png') }}" alt="Logo" class="mx-auto h-24 mb-4 transform transition-transform hover:scale-105 duration-300">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Crea tu cuenta</h2>
            <p class="text-base text-gray-500 dark:text-gray-400">Únete a nuestra comunidad en segundos.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300 text-base">Nombre</label>
                <input id="name" class="block mt-1 w-full py-3 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                @error('name')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300 text-base">Correo Electrónico</label>
                <input id="email" class="block mt-1 w-full py-3 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300 text-base">Contraseña</label>
                <input id="password" class="block mt-1 w-full py-3 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        type="password"
                        name="password"
                        required autocomplete="new-password" />
                @error('password')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300 text-base">Confirmar Contraseña</label>
                <input id="password_confirmation" class="block mt-1 w-full py-3 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-4">
                <a class="text-base underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('¿Ya tienes una cuenta?') }}
                </a>

                <button type="submit" class="ms-4 py-3 px-6 text-base bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
                    {{ __('Registrarse') }}
                </button>
            </div>
        </form>
    </div>
</body>
</html>