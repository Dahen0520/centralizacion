<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Afiliado</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex items-center justify-center min-h-screen font-sans p-6">
    <div class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8 relative z-10 border border-gray-200 dark:border-gray-800 transition-all duration-300 transform hover:scale-[1.01]">
        
        <div class="text-center mb-8">
            <img src="{{ asset('assets/imgs/vertical.png') }}" alt="Logo" class="mx-auto mb-4 w-56 h-auto">
            <p class="text-base text-gray-500 dark:text-gray-400">Ingresa tu DNI e identifícate como un Afiliado.</p>
        </div>

        <form id="afiliado-form" action="{{ route('afiliados.query') }}" method="POST" class="space-y-6">
            @csrf
            <div class="flex items-end space-x-4">
                <div class="flex-1 relative">
                    <label for="dni_busqueda" class="block font-semibold text-gray-700 dark:text-gray-300 mb-2 text-lg">Número de Identificación</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-id-card fa-lg"></i>
                        </span>
                        <input 
                            type="text" 
                            id="dni_busqueda" 
                            name="dni" 
                            required 
                            maxlength="15"
                            placeholder="XXXX-XXXX-XXXXX"
                            class="block w-full pl-12 pr-4 py-3 text-lg rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300 shadow-sm hover:shadow-md"
                        >
                    </div>
                </div>
                <button 
                    type="submit" 
                    class="px-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-400 focus:ring-opacity-50 transition transform hover:scale-105 py-3"
                >
                    Consultar
                </button>
            </div>
        </form>

        <div id="afiliado-response" class="mt-8">
        </div>

    </div>

    <script>
        document.getElementById('dni_busqueda').addEventListener('input', function (e) {
            const input = e.target;
            let value = input.value.replace(/-/g, '');
            
            let formattedValue = '';
            if (value.length > 0) {
                formattedValue += value.substring(0, 4);
            }
            if (value.length > 4) {
                formattedValue += '-' + value.substring(4, 8);
            }
            if (value.length > 8) {
                formattedValue += '-' + value.substring(8, 13);
            }
            
            input.value = formattedValue;
        });

        document.getElementById('afiliado-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const dni = document.getElementById('dni_busqueda').value;
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const responseContainer = document.getElementById('afiliado-response');

            // Limpiar mensajes de error previos
            const existingErrors = responseContainer.querySelectorAll('.error-message-alert');
            existingErrors.forEach(el => el.remove());

            responseContainer.innerHTML = `
                <div class="flex items-center justify-center py-6">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                </div>
            `;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ dni: dni })
                });
                
                const data = await response.json();

                // Limpiar spinner antes de mostrar los datos
                responseContainer.innerHTML = '';
                
                if (response.ok) {
                    if (data.afiliado) {
                        const afiliado = data.afiliado;
                        let buttonText = data.source === 'api' ? 'Siguiente' : 'Confirmar y Continuar';
                        let buttonClass = data.source === 'api' ? 'bg-green-600 hover:bg-green-700' : 'bg-green-600 hover:bg-green-700';
                        let isButtonDisabled = false;
                        
                        responseContainer.innerHTML = `
                            <h4 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Datos del Afiliado</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <label for="dni_resultado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DNI</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="dni_resultado" value="${dni}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="nombre" value="${afiliado.nombre_afiliado || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="genero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Género</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="genero" value="${afiliado.genero || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Nacimiento</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="fecha_nacimiento" value="${new Date(afiliado.fecha_de_nacimiento).toLocaleDateString('es-HN') || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="correo_electronico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo Electrónico</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="correo_electronico" value="${afiliado.correo_electronico || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="telefono" value="${afiliado.telefono || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="departamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="departamento" value="${afiliado.nombre_departamento || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="municipio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Municipio</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="municipio" value="${afiliado.nombre_municipio || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="barrio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" id="barrio" value="${afiliado.nombre_barrio || 'N/A'}" readonly>
                                </div>
                                <div class="col-span-1">
                                    <label for="rtn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RTN</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-green-500 ring-2 ring-green-300 dark:border-green-400 dark:ring-green-600 dark:bg-gray-800 dark:text-white" id="rtn" value="${afiliado.rtn || ''}">
                                </div>
                                <div class="col-span-1">
                                    <label for="numero_cuenta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Cuenta</label>
                                    <input type="text" class="block w-full py-2 text-base rounded-lg border-green-500 ring-2 ring-green-300 dark:border-green-400 dark:ring-green-600 dark:bg-gray-800 dark:text-white" id="numero_cuenta" value="${afiliado.numero_cuenta || ''}">
                                </div>
                                <input type="hidden" id="fecha_nacimiento_hidden" value="${afiliado.fecha_de_nacimiento}">
                                <input type="hidden" id="nombre_afiliado_hidden" value="${afiliado.nombre_afiliado}">
                                <input type="hidden" id="genero_hidden" value="${afiliado.genero}">
                                <input type="hidden" id="email_hidden" value="${afiliado.correo_electronico}">
                                <input type="hidden" id="telefono_hidden" value="${afiliado.telefono}">
                                <input type="hidden" id="departamento_hidden" value="${afiliado.nombre_departamento}">
                                <input type="hidden" id="municipio_hidden" value="${afiliado.nombre_municipio}">
                                <input type="hidden" id="barrio_hidden" value="${afiliado.nombre_barrio}">
                                <input type="hidden" id="rtn_hidden" value="${afiliado.rtn || ''}">
                                <input type="hidden" id="numero_cuenta_hidden" value="${afiliado.numero_cuenta || ''}">
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button type="button" id="registrar-btn" class="px-6 py-3 text-white rounded-md font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-all duration-300 transform ${buttonClass}" ${isButtonDisabled ? 'disabled' : ''}>
                                    ${buttonText}
                                </button>
                            </div>
                        `;
                        // Se agrega el event listener al botón de registro/edición dinámicamente
                        if (!isButtonDisabled) {
                            document.getElementById('registrar-btn').addEventListener('click', async function() {
                                const btn = this;
                                btn.disabled = true;
                                btn.textContent = 'Registrando...';

                                // Limpiar mensajes de error previos
                                const existingErrors = responseContainer.querySelectorAll('.error-message-alert');
                                existingErrors.forEach(el => el.remove());
                                
                                // Validar que el número de cuenta no esté vacío.
                                const numeroCuenta = document.getElementById('numero_cuenta').value;
                                if (!numeroCuenta || numeroCuenta.trim() === '') {
                                    const errorMessageDiv = document.createElement('div');
                                    errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                                    errorMessageDiv.textContent = 'El campo Número de Cuenta es obligatorio.';
                                    responseContainer.prepend(errorMessageDiv);
                                    btn.disabled = false;
                                    btn.textContent = 'Registrar Afiliado';
                                    return; // Detiene la ejecución si la validación falla
                                }

                                const payload = {
                                    dni: document.getElementById('dni_resultado').value,
                                    nombre: document.getElementById('nombre_afiliado_hidden').value,
                                    genero: document.getElementById('genero_hidden').value,
                                    fecha_nacimiento: document.getElementById('fecha_nacimiento_hidden').value,
                                    email: document.getElementById('email_hidden').value,
                                    telefono: document.getElementById('telefono_hidden').value,
                                    departamento_nombre: document.getElementById('departamento_hidden').value,
                                    municipio_nombre: document.getElementById('municipio_hidden').value,
                                    barrio: document.getElementById('barrio_hidden').value,
                                    rtn: document.getElementById('rtn').value,
                                    numero_cuenta: numeroCuenta,
                                    _token: '{{ csrf_token() }}'
                                };

                                try {
                                    const registerResponse = await fetch('{{ route('afiliados.register') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': payload._token,
                                        },
                                        body: JSON.stringify(payload)
                                    });

                                    const registerData = await registerResponse.json();

                                    if (registerResponse.ok) {
                                        window.location.href = `{{ route('empresas.create') }}?afiliado_id=${registerData.afiliado_id}`;
                                    } else {
                                        const errorMessages = Object.values(registerData.errors).flat().join('<br>');
                                        const errorMessageDiv = document.createElement('div');
                                        errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                                        errorMessageDiv.innerHTML = registerData.message || errorMessages;
                                        responseContainer.prepend(errorMessageDiv);
                                        btn.disabled = false;
                                        btn.textContent = 'Registrar Afiliado';
                                    }
                                } catch (error) {
                                    const errorMessageDiv = document.createElement('div');
                                    errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                                    errorMessageDiv.textContent = 'Ocurrió un error inesperado al registrar.';
                                    responseContainer.prepend(errorMessageDiv);
                                    console.error('Error:', error);
                                    btn.disabled = false;
                                    btn.textContent = 'Registrar Afiliado';
                                }
                            });
                        }

                    } else if (data.error) {
                        const errorMessageDiv = document.createElement('div');
                        errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                        errorMessageDiv.textContent = data.error;
                        responseContainer.prepend(errorMessageDiv);
                    } else if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('<br>');
                        const errorMessageDiv = document.createElement('div');
                        errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                        errorMessageDiv.innerHTML = errorMessages;
                        responseContainer.prepend(errorMessageDiv);
                    } else {
                        const errorMessageDiv = document.createElement('div');
                        errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                        errorMessageDiv.textContent = 'Respuesta inesperada del servidor.';
                        responseContainer.prepend(errorMessageDiv);
                    }
                } else {
                    const errorMessageDiv = document.createElement('div');
                    errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                    errorMessageDiv.textContent = `Error en la petición: ${response.status}`;
                    responseContainer.prepend(errorMessageDiv);
                }
            } catch (error) {
                const errorMessageDiv = document.createElement('div');
                errorMessageDiv.className = 'bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4 error-message-alert';
                errorMessageDiv.textContent = 'Ocurrió un error inesperado. Intente de nuevo.';
                responseContainer.prepend(errorMessageDiv);
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>