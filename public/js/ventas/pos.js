function posModule(tiposPago) {
    return {
        // Estado general
        tiendaId: '',
        searchQuery: '',
        allProductos: [],
        filteredProductos: [],
        cart: [],
        
        // Variables de control para prevenir doble clic/escaneo rápido
        lastAddedProductId: null, 
        lastAddedTimestamp: 0, 

        // Variables de control para el carrito
        lastCartUpdateId: null,
        lastCartUpdateTimestamp: 0,

        // Totales
        total: 0.00, 
        discount: 0,
        finalTotal: 0.00,

        // Estados fiscales
        subtotalNeto: 0.00,     
        totalExento: 0.00,      
        totalGravado: 0.00,     
        totalIsv: 0.00,         
        totalImpuestos: 0.00,   

        isProcessing: false,
        isProductsLoading: false,
        showKeyboardShortcuts: false,

        // Cliente
        clientId: null,
        clientSearchQuery: '',
        clientSearchResults: [],
        selectedClientName: 'Cliente Genérico / Sin Registro',
        selectedClientIndex: 0,
        isClientLoading: false,

        // Modal cliente
        showNewClientModal: false,
        newClientSaving: false,
        newClientError: '', 
        newClientForm: { nombre: '', identificacion: '', email: '', telefono: '' },
        
        // Estado de pago
        tiposPago: tiposPago, 
        selectedPaymentType: 'EFECTIVO',

        // Monto recibido y cambio
        amountReceived: 0,
        change: 0,

        init() {
            if (this.tiendaId) this.fetchProductos();
            this.updateCart();
            this.showWelcomeNotification();
        },

        // ==========================================
        // LÓGICA DE PAGO
        // ==========================================
        
        calculateChange() {
            const received = parseFloat(this.amountReceived) || 0;
            const total = parseFloat(this.finalTotal) || 0;
            this.change = Math.max(0, received - total);
        },

        isSaleReady(type) {
            if (!this.cart.length || this.tiendaId === '') return false;
            if (!this.selectedPaymentType) return false;
            
            // Factura y Cotización requieren cliente registrado
            if ((type === 'INVOICE' || type === 'QUOTE') && !(this.clientId > 0)) return false;

            // Crédito requiere cliente registrado
            if (this.selectedPaymentType === 'CREDITO' && !(this.clientId > 0)) return false;

            return true;
        },
        
        // ==========================================
        // NOTIFICACIONES
        // ==========================================
        
        showWelcomeNotification() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: 'info',
                title: '¡Bienvenido al POS!',
                text: 'Presiona F1 para buscar productos'
            });
        },

        showNotification(type, title, text) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            Toast.fire({ icon: type, title: title, text: text });
        },

        // ==========================================
        // MANEJO DE TECLADO
        // ==========================================
        
        handleKeyboard(event) {
            if (event.key === 'F1') {
                event.preventDefault();
                if (this.tiendaId) {
                    this.$refs.productSearch?.focus();
                    this.showNotification('info', 'Búsqueda de productos', 'Escribe para buscar');
                }
            }
            
            if (event.key === 'F2') {
                event.preventDefault();
                document.getElementById('client_search_input')?.focus();
                this.showNotification('info', 'Búsqueda de clientes', 'Busca por nombre o RTN');
            }
            
            if (event.key === 'F9') {
                event.preventDefault();
                if (this.isSaleReady('TICKET') && !this.isProcessing) {
                    this.processSale('TICKET');
                }
            }
            
            if (event.ctrlKey && event.key === 'x') {
                event.preventDefault();
                if (this.cart.length) {
                    this.clearCart();
                }
            }

            if (event.key === 'Escape') {
                if (this.showNewClientModal) {
                    this.closeNewClientModal();
                }
            }

            // Navegación en resultados de clientes
            if (document.activeElement.id === 'client_search_input' && this.clientSearchResults.length > 0) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    this.selectedClientIndex = Math.min(
                        this.selectedClientIndex + 1, 
                        this.clientSearchResults.length - 1
                    );
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    this.selectedClientIndex = Math.max(this.selectedClientIndex - 1, 0);
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    this.selectClient(this.clientSearchResults[this.selectedClientIndex]);
                }
            }
        },

        // ==========================================
        // PRODUCTOS
        // ==========================================
        
        async fetchProductos() {
            this.searchQuery = '';
            this.allProductos = [];
            this.filteredProductos = [];
            this.cart = [];
            this.updateCart();
            this.isProductsLoading = true;

            if (!this.tiendaId) {
                this.isProductsLoading = false;
                return;
            }

            try {
                const res = await fetch(`${window.posRoutes.productsByStore}/${this.tiendaId}`);
                if (!res.ok) throw new Error('Error al obtener productos');
                const data = await res.json();
                
                const productos = data.map(p => ({
                    ...p,
                    isv_tasa: p.isv_tasa !== undefined ? parseFloat(p.isv_tasa) : 0.00,
                }));
                
                this.allProductos = productos;
                this.filteredProductos = productos;
                
                this.showNotification('success', 'Productos cargados', `${data.length} productos disponibles`);
            } catch (err) {
                console.error('Error al cargar productos:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'No se pudieron cargar los productos.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                this.isProductsLoading = false;
            }
        },

        filterProductos() {
            const q = (this.searchQuery || '').toLowerCase().trim();
            
            if (!q) {
                this.filteredProductos = this.allProductos;
                return;
            }

            this.filteredProductos = this.allProductos.filter(p => {
                const nombre = (p.producto_nombre || '').toLowerCase();
                const codigo = (p.codigo_marca || '').toLowerCase();
                
                return nombre.includes(q) || codigo.includes(q);
            });

            // Auto-añadir si coincide exactamente con código de barras
            if (this.filteredProductos.length === 1) {
                const producto = this.filteredProductos[0];
                if (producto.codigo_marca && producto.codigo_marca.toLowerCase() === q) {
                    this.addToCart(producto);
                    this.clearSearch();
                }
            }
        },

        clearSearch() {
            this.searchQuery = '';
            this.filteredProductos = this.allProductos;
        },

        // ==========================================
        // CLIENTES
        // ==========================================
        
        async onClientSearchInput() {
            this.clientSearchQuery = this.clientSearchQuery || '';
            this.selectedClientIndex = 0;
            
            if (this.clientSearchQuery.length < 2) {
                this.clientSearchResults = [];
                return;
            }

            this.isClientLoading = true;

            try {
                const res = await fetch(
                    `${window.posRoutes.searchClients}?query=${encodeURIComponent(this.clientSearchQuery)}`
                );
                
                if (!res.ok) {
                    const errorData = await res.json().catch(() => ({ 
                        message: `Error ${res.status}` 
                    }));
                    throw new Error(errorData.message || 'Error de red');
                }
                
                const data = await res.json();
                this.clientSearchResults = data;
            } catch (err) {
                console.error('Error buscando clientes:', err);
                this.clientSearchResults = [];
                this.showNotification('error', 'Error', err.message);
            } finally {
                this.isClientLoading = false;
            }
        },

        selectClient(client) {
            this.clientId = client.id > 0 ? parseInt(client.id) : null; 
            this.selectedClientName = `${client.nombre}${client.identificacion ? ' (' + client.identificacion + ')' : ''}`;
            this.clientSearchQuery = ''; 
            this.clientSearchResults = [];
            this.selectedClientIndex = 0;
            
            this.showNotification('success', 'Cliente seleccionado', client.nombre);
            this.updateCart();
        },
        
        setClientAfterModal(client) {
            if (!client || !client.id || isNaN(parseInt(client.id))) {
                console.error('Cliente inválido');
                this.clearClient(); 
                return;
            }
            
            this.clientId = parseInt(client.id); 
            this.selectedClientName = `${client.nombre}${client.identificacion ? ' (' + client.identificacion + ')' : ''}`;
            this.clientSearchQuery = ''; 
            this.clientSearchResults = [];
            this.updateCart(); 
            
            this.$nextTick(() => {
                this.$refs.productSearch?.focus();
            });
        },

        clearClientSearch() {
            this.clientSearchQuery = '';
            this.clientSearchResults = [];
            this.selectedClientIndex = 0;
        },

        clearClient() {
            this.clientId = null;
            this.selectedClientName = 'Cliente Genérico / Sin Registro';
            this.clientSearchQuery = '';
            this.clientSearchResults = [];
            this.updateCart();
        },

        // ==========================================
        // FORMATO DE CAMPOS
        // ==========================================
        
        formatIdentificacion(event, fieldType) {
            let value = event.target.value.replace(/\D/g, '');
            
            if (value.length > 4 && value.length <= 8) {
                value = value.replace(/^(\d{4})(\d+)/, '$1-$2');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{4})(\d{4})(\d{0,6}).*/, '$1-$2-$3');
            }
            
            if (fieldType === 'clientSearch') {
                this.clientSearchQuery = value;
                this.$nextTick(() => {
                    const el = document.getElementById('client_search_input');
                    if (el) el.value = value;
                });
            } else if (fieldType === 'newClient') {
                this.newClientForm.identificacion = value;
                this.$nextTick(() => {
                    const el = document.getElementById('new_client_identificacion');
                    if (el) el.value = value;
                });
            }
        },

        formatTelefono(event) {
            let value = event.target.value.replace(/\D/g, '');
            
            if (value.length > 4) {
                value = value.replace(/^(\d{4})(\d{0,4}).*/, '$1-$2');
            }
            
            this.newClientForm.telefono = value;
            this.$nextTick(() => {
                const el = document.getElementById('new_client_telefono');
                if (el) el.value = value;
            });
        },

        // ==========================================
        // MODAL NUEVO CLIENTE
        // ==========================================
        
        openNewClientModal() {
            this.resetNewClientForm();
            this.showNewClientModal = true;
            
            this.$nextTick(() => {
                document.querySelector('input[x-model="newClientForm.nombre"]')?.focus();
            });
        },

        closeNewClientModal() {
            this.showNewClientModal = false;
            this.resetNewClientForm();
        },

        resetNewClientForm() {
            this.newClientForm = { nombre: '', identificacion: '', email: '', telefono: '' };
            this.newClientError = '';
        },

        async saveNewClient() {
            this.newClientSaving = true;
            this.newClientError = ''; 

            const payload = {
                _token: window.posRoutes.csrfToken,
                ...this.newClientForm
            };

            try {
                const response = await fetch(window.posRoutes.storeClient, {
                    method: 'POST',
                    headers: {  
                        'Content-Type': 'application/json',  
                        'X-Requested-With': 'XMLHttpRequest'  
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.dispatchEvent(new CustomEvent('new-client-saved', {
                        detail: { client: data.cliente }
                    }));
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cliente Registrado!',
                        text: `${data.cliente.nombre} ha sido guardado.`,
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                    
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.closeNewClientModal();
                        }, 100); 
                    });

                } else if (response.status === 422) {
                    console.error('Error de validación:', data.errors);
                    
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.closeNewClientModal(); 
                        }, 100); 
                    });
                    
                } else {
                    console.error('Error de servidor:', data);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al guardar el cliente.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (err) {
                console.error('Error de conexión:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Crítico',
                    text: 'Error de conexión.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                this.newClientSaving = false;
            }
        },

        // ==========================================
        // CARRITO
        // ==========================================
        
        addToCart(producto) {
            if (producto.stock_actual === 0) {
                this.showNotification('warning', 'Sin stock', 'No hay stock disponible');
                return;
            }

            const now = Date.now();
            if (this.lastAddedProductId === producto.inventario_id && 
                (now - this.lastAddedTimestamp) < 200) {
                return;
            }
            
            this.lastAddedProductId = producto.inventario_id;
            this.lastAddedTimestamp = now;

            const existing = this.cart.find(i => i.inventario_id === producto.inventario_id);
            
            if (existing) {
                if (existing.cantidad < existing.stockMax) {
                    existing.cantidad = existing.cantidad + 1; 
                    this.showNotification('success', 'Cantidad actualizada', 
                        `${producto.producto_nombre} (${existing.cantidad})`);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock máximo',
                        text: `Solo hay ${existing.stockMax} unidades disponibles.`,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } else {
                this.cart.push({
                    inventario_id: producto.inventario_id,
                    nombre: producto.producto_nombre,
                    precio: producto.precio,
                    stockMax: producto.stock_actual,
                    cantidad: 1,
                    isv_tasa: producto.isv_tasa 
                });
                this.showNotification('success', 'Producto añadido', producto.producto_nombre);
            }
            
            this.updateCart();
        },

        removeFromCart(index) {
            const item = this.cart[index];
            
            Swal.fire({
                title: '¿Eliminar producto?',
                text: `Se eliminará "${item.nombre}" del carrito`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.cart.splice(index, 1);
                    this.updateCart();
                    this.showNotification('info', 'Producto eliminado', item.nombre);
                }
            });
        },

        incrementQuantity(index, maxStock) {
            const item = this.cart[index];
            const now = Date.now();

            if (this.lastCartUpdateId === item.inventario_id && 
                (now - this.lastCartUpdateTimestamp) < 200) {
                return; 
            }
            
            this.lastCartUpdateId = item.inventario_id;
            this.lastCartUpdateTimestamp = now;

            if (item.cantidad < maxStock) {
                item.cantidad++; 
                this.updateCart();
            } else {
                this.showNotification('warning', 'Stock máximo', 'No hay más unidades');
            }
        },

        decrementQuantity(index) {
            const item = this.cart[index];
            const now = Date.now();

            if (this.lastCartUpdateId === item.inventario_id && 
                (now - this.lastCartUpdateTimestamp) < 200) {
                return;
            }
            
            this.lastCartUpdateId = item.inventario_id;
            this.lastCartUpdateTimestamp = now;

            if (item.cantidad > 1) {
                item.cantidad--; 
                this.updateCart();
            }
        },

        updateCart() {
            this.cart.forEach(item => {
                if (typeof item.cantidad !== 'number' || item.cantidad < 1 || isNaN(item.cantidad)) {
                    item.cantidad = 1;
                }
                if (item.cantidad > item.stockMax) {
                    item.cantidad = item.stockMax;
                }
                item.cantidad = parseInt(item.cantidad);
            });

            let subtotalNeto = 0;
            let totalExento = 0;
            let totalGravado = 0;
            let totalIsv = 0;
            
            this.cart.forEach(item => {
                const precioUnitario = parseFloat(item.precio) || 0;
                const cantidad = parseInt(item.cantidad) || 0;
                
                const base = precioUnitario * cantidad;
                const tasa = parseFloat(item.isv_tasa) || 0.00;
                const isvMonto = base * tasa;
                
                subtotalNeto += base;  
                totalIsv += isvMonto;

                if (tasa > 0) {
                    totalGravado += base;
                } else {
                    totalExento += base;
                }
                
                item.subtotalBase = base;
                item.isvMonto = isvMonto;
            });

            this.subtotalNeto = parseFloat(subtotalNeto.toFixed(2));
            this.totalExento = parseFloat(totalExento.toFixed(2));
            this.totalGravado = parseFloat(totalGravado.toFixed(2));
            this.totalIsv = parseFloat(totalIsv.toFixed(2));
            this.totalImpuestos = this.totalIsv;  

            const totalConIsv = this.subtotalNeto + this.totalIsv;
            this.total = parseFloat(totalConIsv.toFixed(2));

            let currentDiscount = parseFloat(this.discount) || 0;  

            if (currentDiscount < 0) currentDiscount = 0;
            if (currentDiscount > totalConIsv) currentDiscount = totalConIsv;
            this.discount = parseFloat(currentDiscount.toFixed(2));  
            
            this.finalTotal = parseFloat((totalConIsv - this.discount).toFixed(2));
            
            // Recalcular cambio si es efectivo
            if (this.selectedPaymentType === 'EFECTIVO') {
                this.calculateChange();
            }
        },

        clearCart() {
            if (!this.cart.length) return;

            Swal.fire({
                title: '¿Vaciar el carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.cart = [];
                    this.discount = 0;
                    this.selectedPaymentType = 'EFECTIVO';
                    this.amountReceived = 0;
                    this.change = 0;
                    this.updateCart();
                    this.showNotification('info', 'Carrito vacío', 'Productos eliminados');
                }
            });
        },

        // ==========================================
        // PROCESAR VENTA
        // ==========================================
        
        async processSale(type) {
            if (!this.isSaleReady(type) || this.isProcessing) {
                this.showSaleValidationError(type);
                return;
            }

            const titleMap = {
                'TICKET': '¿Confirmar Factura (Consumidor Final)?',
                'QUOTE': '¿Guardar como Cotización?',
                'INVOICE': '¿Generar Factura?'
            };
            
            const endpointMap = {
                'TICKET': window.posRoutes.storeTicket,
                'QUOTE': window.posRoutes.storeQuote,
                'INVOICE': window.posRoutes.storeInvoice
            };
            
            const successColorMap = {
                'TICKET': '#10b981',
                'QUOTE': '#3b82f6',
                'INVOICE': '#4f46e5'
            };
            
            const confirmButtonTextMap = {
                'TICKET': 'Sí, procesar',
                'QUOTE': 'Sí, guardar',
                'INVOICE': 'Sí, facturar'
            };

            const result = await Swal.fire({
                title: titleMap[type],
                html: `
                    <div class="text-left space-y-2">
                        <p class="text-sm text-gray-600">
                            <strong>Tipo de Pago:</strong> ${this.tiposPago[this.selectedPaymentType] || 'N/A'}
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Cliente:</strong> ${this.selectedClientName}
                        </p>
                        <p class="text-lg font-bold text-emerald-600 mt-3 pt-3 border-t">
                            <strong>Total:</strong> L ${this.finalTotal.toFixed(2)}
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: successColorMap[type],
                cancelButtonColor: '#6b7280',
                confirmButtonText: `<i class="fas fa-check mr-2"></i> ${confirmButtonTextMap[type]}`,
                cancelButtonText: '<i class="fas fa-times mr-2"></i> Cancelar',
                customClass: { popup: 'swal-wide' }
            });

            if (!result.isConfirmed) return;

            this.isProcessing = true;
            const url = endpointMap[type];

            const detalles = this.cart.map(item => ({
                inventario_id: item.inventario_id,
                cantidad: item.cantidad,
                precio_unitario: item.precio,
                isv_tasa: item.isv_tasa  
            }));

            const payload = {
                _token: window.posRoutes.csrfToken,
                tienda_id: this.tiendaId,
                cliente_id: this.clientId,
                tipo_documento: type,
                total_monto: this.total, 
                descuento: this.discount,
                detalles: detalles,
                tipo_pago: this.selectedPaymentType  
            };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {  
                        'Content-Type': 'application/json',  
                        'X-Requested-With': 'XMLHttpRequest'  
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json().catch(err => {
                    console.error("Error JSON.parse", err);
                    throw new Error(`Error de formato (${res.status})`);
                });

                if (res.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: data.documento_id ? `Transacción #${data.documento_id}` : '¡Procesada!',
                        html: `<p class="text-gray-700">${data.message}</p>` + 
                              (data.documento_url ? `<a href="${data.documento_url}" target="_blank" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"><i class="fas fa-print mr-2"></i> Imprimir / Descargar</a>` : ''),
                        confirmButtonColor: successColorMap[type],
                        confirmButtonText: 'Hecho'
                    });

                    this.cart = [];
                    this.discount = 0;
                    this.selectedPaymentType = 'EFECTIVO';
                    this.amountReceived = 0;
                    this.change = 0;
                    this.updateCart();
                    this.fetchProductos();
                    this.clearClient();

                } else if (res.status === 422) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error de Validación',
                        text: data.message || 'No se pudo procesar.',
                        confirmButtonColor: '#f59e0b'
                    });
                    this.fetchProductos();
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Ocurrió un error.',
                        confirmButtonColor: '#ef4444'
                    });
                }

            } catch (err) {
                console.error('Error en processSale:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Crítico',
                    text: err.message || 'Error de conexión.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                this.isProcessing = false;
            }
        },

        showSaleValidationError(type) {
            if (!this.cart.length || this.tiendaId === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: 'Seleccione tienda y añada productos.',
                    confirmButtonColor: '#f59e0b'
                });
            } else if (!this.selectedPaymentType) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tipo de Pago Requerido',
                    text: 'Debe seleccionar un tipo de pago.',
                    confirmButtonColor: '#f59e0b'
                });
            } else if (this.selectedPaymentType === 'CREDITO' && !(this.clientId > 0)) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cliente Requerido',
                    text: 'Crédito requiere cliente registrado.',
                    confirmButtonColor: '#3b82f6'
                });
            } else if ((type === 'INVOICE' || type === 'QUOTE') && !(this.clientId > 0)) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cliente Requerido',
                    text: `Seleccione un cliente para ${type === 'INVOICE' ? 'Factura' : 'Cotización'}.`,
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    };
}