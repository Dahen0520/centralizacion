<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Lista de Afiliados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 border-t-8 border-blue-600">

                <div class="flex justify-between items-center mb-6">
                    <a href="{{ route('afiliados.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> Crear Afiliado
                    </a>
                </div>

                {{-- Barra de búsqueda --}}
                <div class="mb-4">
                    <input type="text" id="search-input" placeholder="Buscar por DNI, nombre, email o municipio..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                @if (session('success'))
                    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <strong class="font-bold">¡Éxito!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DNI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Municipio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="afiliados-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @include('afiliados.partials.table_rows', ['afiliados' => $afiliados])
                        </tbody>
                    </table>
                </div>

                {{-- Enlaces de paginación --}}
                <div id="pagination-links" class="mt-4">
                    {{ $afiliados->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) setTimeout(() => successAlert.style.display = 'none', 3000);

    const searchInput = document.getElementById('search-input');
    const tableBody   = document.getElementById('afiliados-table-body');
    const pagContainer= document.getElementById('pagination-links');
    let searchTimeout;

    function handleDeleteClick(e) {
        e.preventDefault();
        const form = this.closest('form');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(form.action, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success');
                    fetchAfiliados(getCurrentPage());
                } else {
                    Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
            });
        });
    }

    function attachDeleteListeners() {
        const btns = document.querySelectorAll('.delete-btn');
        btns.forEach(b => {
            const clone = b.cloneNode(true);
            b.parentNode.replaceChild(clone, b);
            clone.addEventListener('click', handleDeleteClick);
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        const url = new URL(this.href);
        const page = url.searchParams.get('page') || 1;
        fetchAfiliados(page);
    }

    function attachPaginationListeners() {
        const links = pagContainer.querySelectorAll('a');
        links.forEach(link => {
            const clone = link.cloneNode(true);
            link.parentNode.replaceChild(clone, link);
            clone.addEventListener('click', handlePaginationClick);
        });
    }

    function fetchAfiliados(page = 1) {
        const q = searchInput.value;
        const url = `{{ route('afiliados.index') }}?page=${page}&search=${encodeURIComponent(q)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => {
            if (!r.ok) throw new Error('Respuesta no válida');
            return r.json();
        })
        .then(data => {
            tableBody.innerHTML = data.table_rows;
            pagContainer.innerHTML = data.pagination_links;
            attachDeleteListeners();
            attachPaginationListeners();
        })
        .catch(err => {
            console.error('Error al cargar afiliados:', err);
            Swal.fire('Error de Carga', 'No se pudieron cargar los afiliados.', 'error');
        });
    }

    function getCurrentPage() {
        const params = new URLSearchParams(window.location.search);
        return params.get('page') || 1;
    }

    searchInput.addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchAfiliados(1), 300);
    });

    attachDeleteListeners();
    attachPaginationListeners();
});
</script>
