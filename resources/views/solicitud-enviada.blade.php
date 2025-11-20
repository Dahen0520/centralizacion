{{-- resources/views/solicitud-enviada.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Enviada con Éxito</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .body-animated-gradient {
            background: linear-gradient(-45deg, #0c2d5e, #1e40af, #1e3a8a, #0c2d5e);
            background-size: 400% 400%;
            animation: gradient-animation 15s ease infinite;
        }
        
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); }
            50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.6); }
        }
        
        .icon-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .success-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .card-entrance {
            animation: cardEntrance 0.6s ease-out;
        }
        
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body class="body-animated-gradient text-white flex items-center justify-center min-h-screen p-6">
    
    {{-- Círculos decorativos de fondo --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
    </div>
    
    {{-- Card principal --}}
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl p-12 relative z-10 text-center card-entrance">
        
        {{-- Icono de éxito con animación --}}
        <div class="mb-8 relative">
            <div class="inline-block relative">
                <div class="absolute inset-0 bg-green-500/20 rounded-full blur-2xl success-glow"></div>
                <i class="fas fa-check-circle text-8xl text-green-500 icon-float relative z-10"></i>
            </div>
        </div>
        
        {{-- Título y descripción --}}
        <div class="mb-10">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                ¡Solicitud Enviada <br>con Éxito!
            </h1>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-600 text-xl leading-relaxed max-w-xl mx-auto">
                Tu información ha sido recibida correctamente y está siendo procesada por nuestro equipo.
            </p>
        </div>
        
        {{-- Información adicional --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 mb-8 border border-blue-100">
            <div class="flex items-start justify-center space-x-4 mb-4">
                <div class="bg-blue-600 text-white p-3 rounded-xl shadow-lg">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="text-left flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">¿Qué sigue ahora?</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Nuestro equipo revisará tu solicitud cuidadosamente. <span class="font-semibold text-blue-700">Pronto te contactaremos</span> para informarte sobre el estado de tu registro.
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Botones de acción --}}
        <div class="space-y-4">
            <a href="{{ url('/') }}" 
               class="group inline-flex items-center justify-center w-full px-8 py-4 bg-gradient-to-r from-blue-700 to-blue-800 text-white rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                <i class="fas fa-home mr-3 group-hover:scale-110 transition-transform duration-300"></i> 
                Volver a la Página Principal
            </a>
            
            <p class="text-gray-500 text-sm mt-6 flex items-center justify-center space-x-2">
                <i class="fas fa-heart text-red-500"></i>
                <span>Gracias por confiar en nosotros</span>
            </p>
        </div>
        
        {{-- Detalles decorativos --}}
        <div class="mt-10 flex items-center justify-center space-x-2 text-gray-400">
            <div class="w-8 h-0.5 bg-gray-300 rounded"></div>
            <i class="fas fa-check text-green-500"></i>
            <div class="w-8 h-0.5 bg-gray-300 rounded"></div>
        </div>
    </div>
    
</body>
</html>