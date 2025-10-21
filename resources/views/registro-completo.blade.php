<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Completo</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <style>
        .step-container {
            display: none;
        }
        .step-container.active {
            display: block;
        }

        select:not([multiple]) {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3e%3cpath d='M7 7l3-3 3 3m0 6l-3 3-3-3' stroke='%23d1d5db' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .checkbox-container {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 1rem;
            max-height: 220px;
            overflow-y: auto;
            -webkit-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
            box-shadow: inset 0 1px 2px rgb(0 0 0 / 0.05);
        }

        .checkbox-container:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #3b82f6;
            outline: none;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.6rem;
            cursor: pointer;
            padding: 0.5rem 0.6rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s ease-in-out;
            font-weight: 500;
        }

        .checkbox-item:hover {
            background-color: #eff6ff;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 0.8rem;
            width: 1.1rem;
            height: 1.1rem;
            accent-color: #3b82f6;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .summary-table th, .summary-table td {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            text-align: left;
            font-size: 0.9rem;
        }

        .summary-table th {
            background-color: #f8fafc;
            font-weight: 600;
        }
        
        /* Estilo para los productos agregados en el Paso 3 */
        .product-tag {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: #e0f2f7; /* Tailwind blue-50 */
            border-radius: 9999px; /* Full rounded */
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        .product-tag-name {
            font-weight: 600;
            color: #1f2937; /* Tailwind gray-800 */
        }
        .product-tag-subcat {
            color: #6b7280; /* Tailwind gray-500 */
            margin-left: 0.5rem;
        }
        .product-tag-delete {
            margin-left: 1rem;
            color: #ef4444; /* Tailwind red-500 */
            cursor: pointer;
            transition: color 0.2s;
        }
        .product-tag-delete:hover {
            color: #b91c1c; /* Tailwind red-700 */
        }
    </style>
</head>
<body class="body-animated-gradient dark:dark-body-animated-gradient text-[#1b1b18] dark:text-[#EDEDEC] flex items-center justify-center min-h-screen font-sans p-6">

    <div class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8 relative z-10 border border-gray-200 dark:border-gray-800 transition-all duration-300 transform hover:scale-[1.01]">
        <div class="text-center mb-8">
            <img src="{{ asset('assets/imgs/vertical.png') }}" alt="Logo" class="mx-auto mb-4 w-56 h-auto">
            <p class="text-base text-gray-500 dark:text-gray-400">
                <span id="step-info">Paso 1 de 4: Información del Afiliado</span>
            </p>
        </div>

        <div class="flex items-center justify-between w-full mb-8">
            <div class="flex-1 relative">
                <div class="h-1 bg-gray-300 rounded-full"></div>
                <div id="progress-bar" class="absolute top-0 left-0 h-1 bg-blue-600 rounded-full transition-all duration-500 ease-in-out" style="width: 0%;"></div>
            </div>
            <div id="step-1-indicator" class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm ml-2 bg-blue-600">1</div>
            <div id="step-2-indicator" class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm ml-2 bg-gray-400">2</div>
            <div id="step-3-indicator" class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm ml-2 bg-gray-400">3</div>
            <div id="step-4-indicator" class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm ml-2 bg-gray-400">4</div>
        </div>

        <form id="multi-step-form" method="POST" action="{{ route('registro.store') }}">
            @csrf
            <div id="step-1" class="step-container active">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Verificación del Afiliado</h4>
                <div class="flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex-1 relative w-full">
                        <label for="dni" class="block font-semibold text-gray-700 dark:text-gray-300 mb-2 text-lg">Número de Identificación</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-id-card fa-lg"></i>
                            </span>
                            <input type="text" id="dni" name="dni" required maxlength="15" placeholder="XXXX-XXXX-XXXXX" class="block w-full pl-12 pr-4 py-3 text-lg rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300 shadow-sm hover:shadow-md">
                        </div>
                    </div>
                    <button type="button" id="query-btn" class="px-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold shadow-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-400 focus:ring-opacity-50 transition transform hover:scale-105 py-3 w-full md:w-auto">
                        Consultar
                    </button>
                </div>
                
                <div id="afiliado-response" class="mt-8"></div>

                <div class="flex justify-end mt-8">
                    <button type="button" id="next-step-1" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold shadow-md hover:bg-blue-700 transition-all duration-300" disabled>Siguiente</button>
                </div>
            </div>

            <div id="step-2" class="step-container">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Registro de Empresa</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="nombre_negocio" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Nombre del Negocio</label>
                        <input type="text" name="nombre_negocio" id="nombre_negocio" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ejemplo: Mi Empresa S.A." />
                    </div>
                    <div>
                        <label for="direccion" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Dirección</label>
                        <input type="text" name="direccion" id="direccion" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ejemplo: Calle Falsa 123, Ciudad" />
                    </div>
                    <div>
                        <label for="rubro_id" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Rubro</label>
                        <select name="rubro_id" id="rubro_id" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" disabled selected>Seleccione un rubro</option>
                            @foreach($rubros as $rubro)
                            <option value="{{ $rubro->id }}">{{ $rubro->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tipo_organizacion_id" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Tipo de Organización</label>
                        <select name="tipo_organizacion_id" id="tipo_organizacion_id" required class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" disabled selected>Seleccione una organización</option>
                            @foreach($tiposOrganizacion as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="pais_exportacion_id" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">País de Exportación (Opcional)</label>
                        <select name="pais_exportacion_id" id="pais_exportacion_id" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" disabled selected>Seleccione un país (Opcional)</option>
                            @foreach($paises as $pais)
                            <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-3 text-gray-700 dark:text-gray-300 font-semibold">Tiendas en las que desea participar</label>
                        <div tabindex="0" role="group" aria-labelledby="checkbox-group" class="checkbox-container">
                            @foreach($tiendas as $tienda)
                            <label class="checkbox-item cursor-pointer">
                                <input type="checkbox" name="tiendas[]" value="{{ $tienda->id }}" class="rounded accent-blue-600 focus:ring-blue-500 focus:outline-none" />
                                <span>{{ $tienda->nombre }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-between">
                    <button type="button" id="prev-step-2" class="px-6 py-3 bg-gray-500 text-white rounded-md font-semibold shadow-md hover:bg-gray-600 transition-all duration-300">Atrás</button>
                    <button type="button" id="next-step-2" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold shadow-md hover:bg-blue-700 transition-all duration-300">Siguiente</button>
                </div>
            </div>

            <div id="step-3" class="step-container">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Sugerir Nuevo Producto</h4>
                <p class="text-center mb-6 text-gray-500 dark:text-gray-400 font-semibold text-lg">
                    Productos agregados: <span id="productos-count" class="text-blue-600 dark:text-blue-400">0</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="nombre_producto" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Nombre del Producto</label>
                        <input type="text" id="nombre_producto" name="nombre_producto" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: Jabón Artesanal de Lavanda"/>
                    </div>
                    <div>
                        <label for="subcategoria_id_producto" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Subcategoría</label>
                        <select name="subcategoria_id_producto" id="subcategoria_id_producto" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" disabled selected>Selecciona una subcategoría</option>
                            @foreach($subcategorias as $subcategoria)
                                <option value="{{ $subcategoria->id }}">{{ $subcategoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- NUEVO CAMPO: IMPUESTO --}}
                    <div>
                        <label for="impuesto_id_producto" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Impuesto Aplicable</label>
                        <select name="impuesto_id_producto" id="impuesto_id_producto" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" disabled selected>Selecciona un impuesto</option>
                            @foreach($impuestos as $impuesto)
                                <option value="{{ $impuesto->id }}">{{ $impuesto->nombre }} ({{ number_format($impuesto->porcentaje, 2) }}%)</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- NUEVO CAMPO: PERMITE FACTURACIÓN (CHECKBOX) --}}
                    <div class="flex items-center mt-6">
                        <input type="checkbox" id="permite_facturacion_producto" name="permite_facturacion_producto" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <label for="permite_facturacion_producto" class="ml-3 block text-gray-700 dark:text-gray-300 font-semibold cursor-pointer">
                            Permite Facturación
                        </label>
                        <i class="fas fa-info-circle ml-2 text-gray-400 cursor-pointer" title="Marca si este producto debe ser facturado fiscalmente."></i>
                    </div>

                    <div class="md:col-span-2">
                        <label for="descripcion_producto" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 cursor-pointer">Descripción</label>
                        <textarea id="descripcion_producto" name="descripcion_producto" rows="4" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 text-base shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Describe tu producto con detalle"></textarea>
                    </div>
                </div>

                {{-- LISTA DE PRODUCTOS AGREGADOS (Horizontal) --}}
                <div id="product-list" class="mt-8 flex flex-wrap gap-3"></div>
                <input type="hidden" name="productos_json" id="productos-input">

                <div class="mt-10 flex flex-col sm:flex-row justify-between gap-4">
                    <button type="button" id="prev-step-3" class="px-6 py-3 bg-gray-500 text-white rounded-md font-semibold shadow-md hover:bg-gray-600 transition-all duration-300">Atrás</button>
                    <div class="flex flex-col sm:flex-row justify-end gap-4 w-full sm:w-auto">
                        <button type="button" id="add-product-btn" class="px-8 py-4 bg-green-600 text-white rounded-md font-bold shadow-md hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 transition transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Agregar Producto
                        </button>
                        <button type="button" id="next-step-3" class="px-8 py-4 bg-blue-600 text-white rounded-md font-bold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition transform hover:scale-105" disabled>
                            <i class="fas fa-check-circle mr-2"></i> Continuar
                        </button>
                    </div>
                </div>
            </div>

            <div id="step-4" class="step-container">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Acuerdo de Compromiso</h4>
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-inner mb-6 space-y-4 text-gray-700 dark:text-gray-300 overflow-y-auto" style="max-height: 500px;">
                    <h5 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Resumen de tu Información</h5>
                    <div id="summary-info"></div>

                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">COMPROMISO CON LOS AFILIADOS</h5>
                    <p class="text-sm">Promotora y Comercializadora Cooperativa SA se compromete a dar un servicio de calidad, promoción y comercialización de productos para incrementar ventas e ingresos a nuevos mercados.</p>
                    <ul class="list-disc list-inside text-sm space-y-2">
                        <li>Reportar e informar de ventas de inventario sobre producto.</li>
                        <li>Realizar transferencia bancaria del monto de venta menos comisión en un lapso de no más de 48 Horas después de generar la venta.</li>
                    </ul>

                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">COMPROMISO DEL AFILIADO</h5>
                    <p class="text-sm">El afiliado empresario debe participar activamente en las actividades de activación convocadas por PROCOOPSA, recibir sugerencias y propuestas de mejora, así mismo estar atento a capacitaciones o asesorías debidamente programadas.</p>
                    <ul class="list-disc list-inside text-sm space-y-2">
                        <li>El empresario deberá presentar el producto en buen estado, mismo que deberá llevar su respectivo empaque o envoltura. En caso de ser un producto perecedero es indispensable que vaya debidamente procesado, contando con registro y licencia sanitaria detallada en su empaque.</li>
                        <li>El empresario deberá contar con facturación CAI, la cual deberá ser entregada al llevar el producto, misma que deberá reflejar el ISV.</li>
                        <li>Una vez que el producto se acerque su fecha de caducidad, debe ser remplazado tres días antes de su fecha de vencimiento.</li>
                        <li>Se debe mantener igualdad de precios, es decir el precio que presenta el empresario como valor final del producto deberá ser el mismo valor reflejado en tienda.</li>
                        <li>El empresario debe de mantener como mínimo 3 meses el producto en tienda antes de ser retirado completamente. Solo puede ser rotando las líneas de su producto de forma mensual, con previa notificación a la administradora de tienda.</li>
                        <li>Cuando el emprendedor retire la totalidad de los productos de la tienda, este debe comunicar vía correo electrónico dirigido a la administradora con 30 días de anticipación.</li>
                        <li>Todas las ventas generadas en activaciones/expoferias deben ser reportadas e ingresadas a la tienda, y pagadas en el proceso normal de pagos de las ventas. (En los primeros 5-6 días de cada mes)</li>
                    </ul>

                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">COMISIONES Y MEMBRESIAS</h5>
                    <p class="text-sm">Se retendrá el equivalente a un 12% de comisión por cada venta generada en tienda.</p>
                    <p class="text-sm">Todo empresario cuyas ventas mensuales sean igual o superiores a L 500.00, pagarán membresía mensual de la siguiente forma:</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <th class="py-2 px-4 border-b">Tiempo</th>
                                    <th class="py-2 px-4 border-b">Aporte Fundación Chorotega</th>
                                    <th class="py-2 px-4 border-b">Aporte del empresario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b dark:border-gray-600">
                                    <td class="py-2 px-4">Trimestre 1</td>
                                    <td class="py-2 px-4">L. 1,800.00 (100%)</td>
                                    <td class="py-2 px-4">L. 0.00 (0%)</td>
                                </tr>
                                <tr class="border-b dark:border-gray-600">
                                    <td class="py-2 px-4">Trimestre 2</td>
                                    <td class="py-2 px-4">L. 1,350.00 (75%)</td>
                                    <td class="py-2 px-4">L. 450.00 (25%)</td>
                                </tr>
                                <tr class="border-b dark:border-gray-600">
                                    <td class="py-2 px-4">Trimestre 3</td>
                                    <td class="py-2 px-4">L. 900.00 (50%)</td>
                                    <td class="py-2 px-4">L. 900.00 (50%)</td>
                                </tr>
                                <tr class="border-b dark:border-gray-600">
                                    <td class="py-2 px-4">Trimestre 4</td>
                                    <td class="py-2 px-4">L. 450 (25%)</td>
                                    <td class="py-2 px-4">L. 1,350.00 (75%)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">INVENTARIO</h5>
                    <ul class="list-disc list-inside text-sm space-y-2">
                        <li>Ningún empresario está matriculado a un espacio dentro de la tienda.</li>
                        <li>La administradora de la tienda tiene la libertad de mover los productos y reubicar los en diferentes lugares dentro de la tienda.</li>
                        <li>Todos los productos vencidos o dañados serán retirados de la tienda y entregados al empresario.</li>
                    </ul>
                    
                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">BENEFICIOS PRINCIPALES</h5>
                    <ul class="list-disc list-inside text-sm space-y-2">
                        <li>Asesoría para la mejora del producto.</li>
                        <li>Fotografía profesional del producto.</li>
                        <li>Promoción y comercialización del producto.</li>
                        <li>Fundación Chorotega con el propósito de beneficiar a sus afiliados subsidiará el costo parcial de la membresía por un (1) año, distribuida de la siguiente forma.</li>
                    </ul>

                    <h5 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">TIEMPO DE CONTRATO</h5>
                    <p class="text-sm">Acuerdan las Partes a este Contrato por tiempo indefinido y podrá darse por terminado por cualquiera de las Partes, con o sin justa causa, previo aviso por escrito en un plazo no menor de treinta (30) días calendarios. No obstante, lo anterior, Promotora y Comercializadora Cooperativa SA, podrá dar por terminado este Contrato, sin la necesidad de notificación previa en cualquiera de las siguientes ocurrencias: a) En el supuesto cierre operaciones. b) En el supuesto que el Negocio Afiliado cierre o mantenga inactiva la(s) cuenta(s) bancaria(s) de Pago. c) El embargo, o cualquier medida cautelar judicial, la quiebra, la insolvencia o cualquier hecho o acto que afecte en cualquier forma el funcionamiento normal del (los) establecimiento(s) del Negocio Afiliado.</p>
                </div>
                
                {{-- CHECKBOX DE TÉRMINOS Y CONDICIONES --}}
                <div class="mt-4 mb-8 flex items-start">
                    <input type="checkbox" id="aceptar-terminos" class="h-5 w-5 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <label for="aceptar-terminos" class="ml-3 block text-base font-semibold text-gray-900 dark:text-gray-100 cursor-pointer">
                        Confirmo que he leído y acepto los Términos y Condiciones del Acuerdo de Compromiso.
                    </label>
                </div>

                <div class="mt-10 flex justify-between">
                    <button type="button" id="prev-step-4" class="px-6 py-3 bg-gray-500 text-white rounded-md font-semibold shadow-md hover:bg-gray-600 transition-all duration-300">Atrás</button>
                    <button type="submit" id="submit-btn" class="px-8 py-4 bg-blue-600 text-white rounded-md font-bold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition transform hover:scale-105" disabled>
                        <i class="fas fa-check-circle mr-2"></i> Confirmar y Finalizar
                    </button>
                </div>
            </div>

        </form>
    </div>

    <script>
        // Lógica de validación y navegación del formulario por pasos
        let currentStep = 1;
        const steps = ['step-1', 'step-2', 'step-3', 'step-4'];
        const stepInfo = document.getElementById('step-info');
        const progressBar = document.getElementById('progress-bar');
        const form = document.getElementById('multi-step-form');
        const submitBtn = document.getElementById('submit-btn'); 
        
        let productosAgregados = [];
        const productosCountSpan = document.getElementById('productos-count');
        const productListDiv = document.getElementById('product-list');
        const nextStep3Btn = document.getElementById('next-step-3');
        const summaryInfoDiv = document.getElementById('summary-info');
        const aceptarTerminosCheckbox = document.getElementById('aceptar-terminos'); 

        function updateProgress(step) {
            const progress = (step - 1) * 100 / (steps.length - 1);
            progressBar.style.width = `${progress}%`;

            for (let i = 0; i < steps.length; i++) {
                const indicator = document.getElementById(`step-${i + 1}-indicator`);
                if (i + 1 < step) {
                    indicator.classList.remove('bg-gray-400');
                    indicator.classList.add('bg-blue-600');
                } else if (i + 1 === step) {
                    indicator.classList.add('bg-blue-600');
                    indicator.classList.remove('bg-gray-400');
                } else {
                    indicator.classList.remove('bg-blue-600');
                    indicator.classList.add('bg-gray-400');
                }
            }

            if (step === 1) stepInfo.textContent = 'Paso 1 de 4: Información del Afiliado';
            if (step === 2) stepInfo.textContent = 'Paso 2 de 4: Información de la Empresa';
            if (step === 3) stepInfo.textContent = 'Paso 3 de 4: Sugerir Productos';
            if (step === 4) stepInfo.textContent = 'Paso 4 de 4: Contrato y Finalizar';
        }
        
        function showStep(step) {
            steps.forEach((stepId, index) => {
                const element = document.getElementById(stepId);
                if (index + 1 === step) {
                    element.classList.add('active');
                } else {
                    element.classList.remove('active');
                }
            });
            currentStep = step; 
            updateProgress(step);
            
            if (step === 4) {
                checkTermsAgreement();
            }
        }

        function validateStep1() {
            const responseContainer = document.getElementById('afiliado-response');
            const dniInput = document.getElementById('dni').value;

            if (responseContainer.innerHTML.trim() === '' || document.querySelector('#afiliado-response input[name="nombre_afiliado"]') === null) {
                responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">Por favor, consulta y verifica los datos de un afiliado antes de continuar.</div>`;
                return false;
            }

            const numeroCuenta = document.querySelector('#afiliado-response input[name="numero_cuenta"]').value;
            const rtnInput = document.querySelector('#afiliado-response input[name="rtn"]');

            if (!numeroCuenta.trim()) {
                responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">El Número de Cuenta es un campo obligatorio.</div>`;
                return false;
            }
            
            if (rtnInput) {
                const rawRtn = rtnInput.value.replace(/[^0-9]/g, '');
                if (rawRtn.length !== 14) {
                     responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">El RTN debe estar completo (14 caracteres numéricos).</div>`;
                     return false;
                }
            }

            return true;
        }

        function validateStep2() {
            const nombreNegocio = document.getElementById('nombre_negocio').value;
            const direccion = document.getElementById('direccion').value;
            const rubroId = document.getElementById('rubro_id').value;
            const tipoOrgId = document.getElementById('tipo_organizacion_id').value;
            const tiendas = document.querySelectorAll('input[name="tiendas[]"]:checked');

            if (!nombreNegocio || !direccion || !rubroId || !tipoOrgId || tiendas.length === 0) {
                alert('Por favor, complete todos los campos obligatorios del Paso 2 (incluyendo seleccionar al menos una tienda).');
                return false;
            }
            return true;
        }
        
        function validateStep3() {
            if (productosAgregados.length === 0) {
                alert('Debe agregar al menos un producto para continuar.');
                return false;
            }
            
            const hasMissingImpuesto = productosAgregados.some(p => !p.impuesto_id);
            if (hasMissingImpuesto) {
                 alert('Debe seleccionar un Impuesto Aplicable para cada producto agregado.');
                 return false;
            }
            return true;
        }

        function buildSummary() {
            let afiliadoInfo = {};
            const afiliadoInputs = document.querySelectorAll('#afiliado-response input');
            afiliadoInputs.forEach(input => {
                const name = input.name;
                const value = input.value;
                if (name) {
                    afiliadoInfo[name] = value;
                }
            });

            let empresaInfo = {};
            const empresaInputs = document.querySelectorAll('#step-2 input, #step-2 select');
            empresaInputs.forEach(input => {
                const name = input.name;
                const value = input.value;
                if (name) {
                    if (input.type === 'checkbox') {
                         if (input.checked) {
                             if (!empresaInfo[name]) empresaInfo[name] = [];
                             empresaInfo[name].push(input.nextElementSibling.textContent);
                         }
                    } else {
                        empresaInfo[name] = value;
                    }
                }
            });

            const rubroText = document.querySelector(`#rubro_id option[value="${empresaInfo.rubro_id}"]`)?.textContent || 'N/A';
            const tipoOrgText = document.querySelector(`#tipo_organizacion_id option[value="${empresaInfo.tipo_organizacion_id}"]`)?.textContent || 'N/A';
            const paisExportacionText = document.querySelector(`#pais_exportacion_id option[value="${empresaInfo.pais_exportacion_id}"]`)?.textContent || 'No aplica';
            
            let tiendasText = empresaInfo.tiendas?.join(', ') || 'N/A';

            let afiliadoHtml = `<table class="summary-table"><tbody>`;
            afiliadoHtml += `<tr><th colspan="2" class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Información del Afiliado</th></tr>`;
            afiliadoHtml += `<tr><td><strong>DNI:</strong></td><td>${afiliadoInfo.dni || 'N/A'}</td></tr>`;
            afiliadoHtml += `<tr><td><strong>Nombre:</strong></td><td>${afiliadoInfo.nombre_afiliado || 'N/A'}</td></tr>`;
            afiliadoHtml += `<tr><td><strong>RTN:</strong></td><td>${rtnFormat(afiliadoInfo.rtn) || 'N/A'}</td></tr>`; // Formateado para resumen
            afiliadoHtml += `<tr><td><strong>Número de Cuenta:</strong></td><td>${afiliadoInfo.numero_cuenta || 'N/A'}</td></tr>`;
            afiliadoHtml += `</tbody></table>`;
            
            let empresaHtml = `<table class="summary-table"><tbody>`;
            empresaHtml += `<tr><th colspan="2" class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Información de la Empresa</th></tr>`;
            empresaHtml += `<tr><td><strong>Nombre del Negocio:</strong></td><td>${empresaInfo.nombre_negocio || 'N/A'}</td></tr>`;
            empresaHtml += `<tr><td><strong>Dirección:</strong></td><td>${empresaInfo.direccion || 'N/A'}</td></tr>`;
            empresaHtml += `<tr><td><strong>Rubro:</strong></td><td>${rubroText}</td></tr>`;
            empresaHtml += `<tr><td><strong>Tipo de Organización:</strong></td><td>${tipoOrgText}</td></tr>`;
            empresaHtml += `<tr><td><strong>País de Exportación:</strong></td><td>${paisExportacionText}</td></tr>`;
            empresaHtml += `<tr><td><strong>Tiendas en las que desea participar:</strong></td><td>${tiendasText}</td></tr>`;
            empresaHtml += `</tbody></table>`;

            let productsHtml = `<table class="summary-table"><tbody>`;
            productsHtml += `<tr><th colspan="4" class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Productos Sugeridos (${productosAgregados.length})</th></tr>`;
            if (productosAgregados.length > 0) {
                productsHtml += `<tr><td class="font-semibold">Nombre</td><td class="font-semibold">Subcategoría</td><td class="font-semibold">Impuesto</td><td class="font-semibold">Facturación</td></tr>`;
                productosAgregados.forEach((p, index) => {
                    const subcatName = document.querySelector(`#subcategoria_id_producto option[value="${p.subcategoria_id}"]`)?.textContent || 'N/A';
                    const impuestoText = document.querySelector(`#impuesto_id_producto option[value="${p.impuesto_id}"]`)?.textContent || 'N/A';
                    const facturacionText = p.permite_facturacion ? 'Sí' : 'No';

                    productsHtml += `<tr>
                                        <td>${p.nombre}</td>
                                        <td>${subcatName.split('(')[0].trim()}</td>
                                        <td>${impuestoText}</td>
                                        <td>${facturacionText}</td>
                                     </tr>`;
                });
            } else {
                productsHtml += `<tr><td colspan="4" class="text-center">No se agregaron productos.</td></tr>`;
            }
            productsHtml += `</tbody></table>`;
            
            summaryInfoDiv.innerHTML = afiliadoHtml + empresaHtml + productsHtml;
        }

        function addProduct() {
            const nombre = document.getElementById('nombre_producto');
            const subcategoriaId = document.getElementById('subcategoria_id_producto');
            const impuestoId = document.getElementById('impuesto_id_producto');
            const permiteFacturacion = document.getElementById('permite_facturacion_producto');
            const descripcion = document.getElementById('descripcion_producto');

            if (!nombre.value.trim() || !subcategoriaId.value.trim() || !impuestoId.value.trim()) {
                alert('Por favor, complete el nombre, la subcategoría y el impuesto del producto.');
                return;
            }

            const newProduct = {
                nombre: nombre.value,
                subcategoria_id: subcategoriaId.value,
                impuesto_id: impuestoId.value,
                permite_facturacion: permiteFacturacion.checked,
                descripcion: descripcion.value
            };
            
            productosAgregados.push(newProduct);
            productosCountSpan.textContent = productosAgregados.length;
            
            // Limpiar campos
            nombre.value = '';
            subcategoriaId.value = '';
            impuestoId.value = '';
            permiteFacturacion.checked = false;
            descripcion.value = '';

            nextStep3Btn.disabled = false;

            // Renderizar la lista
            renderProductList();
        }

        function removeProduct(index) {
            productosAgregados.splice(index, 1);
            productosCountSpan.textContent = productosAgregados.length;
            
            renderProductList(); // Volver a renderizar

            if (productosAgregados.length === 0) {
                nextStep3Btn.disabled = true;
            }
        }
        
        // Hacer la función removeProduct accesible globalmente
        window.removeProduct = removeProduct; 

        function renderProductList() {
            productListDiv.innerHTML = '';
            productosAgregados.forEach((product, i) => {
                const subcatName = document.querySelector(`#subcategoria_id_producto option[value="${product.subcategoria_id}"]`)?.textContent || 'N/A';
                const impuestoName = document.querySelector(`#impuesto_id_producto option[value="${product.impuesto_id}"]`)?.textContent || 'N/A';
                
                const facturacionIcon = product.permite_facturacion 
                    ? '<i class="fas fa-check-circle text-green-500"></i>' 
                    : '<i class="fas fa-times-circle text-red-500"></i>';

                const productHtml = `
                    <div class="product-tag bg-gray-100 dark:bg-gray-800 shadow-sm transition-all">
                        <span class="product-tag-name">${product.nombre}</span> 
                        <span class="product-tag-subcat">(${subcatName.split('(')[0].trim()})</span>
                        <span class="product-tag-subcat">| ${impuestoName.split('(')[0].trim()}</span>
                        <span class="ml-2">${facturacionIcon}</span>
                        <button type="button" class="product-tag-delete" onclick="removeProduct(${i})">
                            <i class="fas fa-trash-alt text-base"></i>
                        </button>
                    </div>
                `;
                productListDiv.insertAdjacentHTML('beforeend', productHtml);
            });
        }
        
        function checkTermsAgreement() {
            submitBtn.disabled = !aceptarTerminosCheckbox.checked;
        }

        // --- FUNCIONES DE FORMATO Y MÁSCARA ---
        function rtnFormat(value) {
            if (!value) return '';
            const raw = value.replace(/[^0-9]/g, '').substring(0, 14); 
            let formatted = '';
            if (raw.length > 8) formatted = `${raw.substring(0, 4)}-${raw.substring(4, 8)}-${raw.substring(8, 14)}`;
            else if (raw.length > 4) formatted = `${raw.substring(0, 4)}-${raw.substring(4, 8)}`;
            else formatted = raw;
            return formatted;
        }

        function applyRtnMask(e) {
            const input = e.target;
            const start = input.selectionStart;
            
            let rawValue = input.value.replace(/[^0-9-]/g, '');
            if (e.data && /[^0-9]/.test(e.data)) {
                 e.preventDefault();
                 return;
            }
            
            const rawValuePure = rawValue.replace(/[^0-9]/g, '');
            
            if (rawValuePure.length > 14 && e.inputType !== 'deleteContentBackward') {
                e.preventDefault();
                input.value = rtnFormat(rawValuePure.substring(0, 14));
                return;
            }
            
            const formatted = rtnFormat(rawValuePure);
            input.value = formatted;

            let newPosition = start;
            
            const guionesAntes = (formatted.substring(0, start).match(/-/g) || []).length;
            const rawValueAntes = input.value.substring(0, start).replace(/[^0-9]/g, '');
            
            newPosition = rawValueAntes.length + guionesAntes;

            if (input.value.charAt(newPosition - 1) === '-' && e.inputType !== 'deleteContentBackward') {
                newPosition++;
            }
            
            if (newPosition > formatted.length) {
                newPosition = formatted.length;
            }

            input.setSelectionRange(newPosition, newPosition);
        }
        
        function buildAndSubmitForm() {
            // Validaciones
            if (!validateStep1()) {
                showStep(1);
                return;
            }
            if (!validateStep2()) {
                showStep(2);
                return;
            }
            if (!validateStep3()) {
                showStep(3);
                return;
            }
            if (!aceptarTerminosCheckbox.checked) {
                alert('Debe aceptar los Términos y Condiciones para finalizar el registro.');
                return;
            }
            
            const formData = new FormData();
            formData.append('_token', form.querySelector('input[name="_token"]').value);

            // --- RECOLECCIÓN DE DATOS (VERSION ESTABLE) ---
            
            // Paso 1: Afiliado (recolectando todos los inputs, incluidos los ocultos)
            const afiliadoInputs = document.querySelectorAll('#afiliado-response input');
            afiliadoInputs.forEach(input => {
                const name = input.name;
                let value = input.value || '';

                if (name === 'rtn') {
                    // Limpiar RTN de guiones para el backend
                    value = value.replace(/[^0-9]/g, '');
                }
                
                // Mapear 'nombre_afiliado' a 'nombre'
                const nameMap = {
                    'nombre_afiliado': 'nombre',
                    'correo_electronico': 'email',
                    'fecha_de_nacimiento': 'fecha_nacimiento',
                    'nombre_departamento': 'departamento_nombre',
                    'nombre_municipio': 'municipio_nombre',
                    // Mantener el resto
                };
                
                // Usar el nombre mapeado o el original
                formData.append(nameMap[name] || name, value);
            });
            
            // Paso 2: Empresa
            const empresaInputs = document.querySelectorAll('#step-2 input, #step-2 select');
            empresaInputs.forEach(input => {
                if (input.type === 'checkbox') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    }
                } else {
                    formData.append(input.name, input.value || '');
                }
            });

            // Paso 3: Productos como JSON
            formData.append('productos_json', JSON.stringify(productosAgregados));

            // Deshabilitar botón para evitar doble click
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            // Envía el formulario de manera asíncrona 
            fetch(form.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json().then(errorData => {
                        console.error('Error de validación:', errorData.errors);
                        const errors = Object.values(errorData.errors).flat().join('<br>');
                        alert('Error al registrar: ' + errors);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirmar y Finalizar';
                    }).catch(() => {
                        throw new Error("Respuesta de servidor inesperada.");
                    });
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                alert('Ocurrió un error inesperado al enviar el formulario.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Confirmar y Finalizar';
            });
        }

        // --- Event listeners ---
        document.getElementById('dni').addEventListener('input', function (e) {
            const input = e.target;
            let value = input.value.replace(/-/g, '');
            let formattedValue = '';
            if (value.length > 0) formattedValue += value.substring(0, 4);
            if (value.length > 4) formattedValue += '-' + value.substring(4, 8);
            if (value.length > 8) formattedValue += '-' + value.substring(8, 13);
            input.value = formattedValue;
        });
        
        document.addEventListener('input', function(e) {
            if (e.target.name === 'rtn') {
                applyRtnMask(e);
            }
        });

        document.getElementById('query-btn').addEventListener('click', async function() {
            const dni = document.getElementById('dni').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;
            const responseContainer = document.getElementById('afiliado-response');
            const nextBtn = document.getElementById('next-step-1');
            const queryBtn = this;
            responseContainer.innerHTML = `<div class="flex items-center justify-center py-6"><i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i></div>`;
            nextBtn.disabled = true;
            queryBtn.disabled = true;

            try {
                const response = await fetch("{{ route('afiliados.query') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ dni: dni })
                });
                
                const data = await response.json();
                responseContainer.innerHTML = '';
                queryBtn.disabled = false;

                if (response.ok) {
                    if (data.afiliado) {
                        const afiliado = data.afiliado;
                        const initialRtn = afiliado.rtn && afiliado.rtn.length === 14 ? afiliado.rtn : dni.replace(/-/g, '');
                        
                        let html = `
                            <h4 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Datos del Afiliado</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <input type="hidden" name="dni" value="${dni}">
                                <input type="hidden" name="genero" value="${afiliado.genero || ''}">
                                <input type="hidden" name="fecha_de_nacimiento" value="${afiliado.fecha_de_nacimiento || ''}">
                                <input type="hidden" name="correo_electronico" value="${afiliado.correo_electronico || ''}">
                                <input type="hidden" name="telefono" value="${afiliado.telefono || ''}">
                                <input type="hidden" name="nombre_departamento" value="${afiliado.nombre_departamento || ''}">
                                <input type="hidden" name="nombre_municipio" value="${afiliado.nombre_municipio || ''}">
                                <input type="hidden" name="barrio" value="${afiliado.nombre_barrio || ''}">

                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre Completo</label>
                                    <input type="text" name="nombre_afiliado" class="block w-full py-2 text-base rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white font-bold" value="${afiliado.nombre_afiliado || ''}" readonly>
                                </div>
                                
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RTN (14 dígitos)</label>
                                    <input type="text" name="rtn" maxlength="16" class="block w-full py-2 text-base rounded-lg border-green-500 ring-2 ring-green-300 dark:border-green-400 dark:ring-green-600 dark:bg-gray-800 dark:text-white" value="${rtnFormat(initialRtn)}" required>
                                </div>
                                
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Cuenta Retirable</label>
                                    <input type="text" name="numero_cuenta" class="block w-full py-2 text-base rounded-lg border-green-500 ring-2 ring-green-300 dark:border-green-400 dark:ring-green-600 dark:bg-gray-800 dark:text-white" value="${afiliado.numero_cuenta || ''}" required>
                                </div>
                            </div>
                        `;
                        responseContainer.innerHTML = html;
                        nextBtn.disabled = false;
                    } else if (data.error) {
                        responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">${data.error}</div>`;
                    }
                } else {
                    const errorData = await response.json();
                    const errors = Object.values(errorData.errors).flat().join('<br>');
                    responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">${errors}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                responseContainer.innerHTML = `<div class="bg-red-500 text-white font-bold p-4 rounded-lg text-center mt-4">Ocurrió un error inesperado.</div>`;
                queryBtn.disabled = false;
            }
        });

        document.getElementById('next-step-1').addEventListener('click', function() {
            if (validateStep1()) {
                showStep(2);
            }
        });

        document.getElementById('prev-step-2').addEventListener('click', function() {
            showStep(1);
        });

        document.getElementById('next-step-2').addEventListener('click', function() {
            if (validateStep2()) {
                showStep(3);
            }
        });

        document.getElementById('prev-step-3').addEventListener('click', function() {
            showStep(2);
        });
        
        document.getElementById('add-product-btn').addEventListener('click', addProduct);

        document.getElementById('next-step-3').addEventListener('click', function() {
            if (validateStep3()) {
                buildSummary();
                showStep(4);
            }
        });

        document.getElementById('prev-step-4').addEventListener('click', function() {
            showStep(3);
        });
        
        // --- Validar Checkbox de Términos y Condiciones ---
        aceptarTerminosCheckbox.addEventListener('change', checkTermsAgreement);

        document.getElementById('submit-btn').addEventListener('click', function(event) {
            event.preventDefault();
            buildAndSubmitForm();
        });
        
        showStep(1);
    </script>
</body>
</html>