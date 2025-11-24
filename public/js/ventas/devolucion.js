document.addEventListener('alpine:init', () => {
    Alpine.data('devolucionModule', () => {
        const data = window.DevolucionData || {};
        let detallesPorId = {};
        
        try {
            const jsonString = data.detallesJson;
            if (jsonString && jsonString.length > 0) {
                detallesPorId = JSON.parse(jsonString);
            }
        } catch (e) {
            console.error("Error al inicializar JSON:", e);
            detallesPorId = {};
        }

        return {
            totalDevolucion: 0.00,
            productosDevolver: {}, 
            detallesPorId: detallesPorId,
            
            init() {
                this.updateDevolucionTotal(); 
            },
            
            updateDevolucionTotal() {
                let total = 0;
                for (const detalleId in this.productosDevolver) {
                     if (this.productosDevolver.hasOwnProperty(detalleId)) {
                         const cantidadDevuelta = parseFloat(this.productosDevolver[detalleId]);
                         const detalle = this.detallesPorId[detalleId];
                         
                         if (detalle && cantidadDevuelta > 0) {
                             const montoUnitarioBase = parseFloat(detalle.precio_unitario);
                             const isvTasa = parseFloat(detalle.isv_tasa);
                             
                             const montoBase = cantidadDevuelta * montoUnitarioBase;
                             const isvMonto = Math.round(montoBase * isvTasa * 100) / 100; 
                             const totalLineaDevuelta = montoBase + isvMonto;
                             
                             total += totalLineaDevuelta;
                         }
                     }
                }
                this.totalDevolucion = total;
            },
            
            handleSubmit(event) {
                if (Object.keys(this.productosDevolver).length === 0) {
                    alert('Debe seleccionar al menos un producto para devolver.');
                    event.preventDefault(); 
                    return;
                }
                
                const form = event.target;
                
                const existingFields = form.querySelectorAll('input[name^="devoluciones"]');
                existingFields.forEach(field => field.remove());
                
                let index = 0;
                for (const detalleId in this.productosDevolver) {
                    if (this.productosDevolver.hasOwnProperty(detalleId)) {
                        const cantidad = this.productosDevolver[detalleId];
                        
                        const inputDetalleId = document.createElement('input');
                        inputDetalleId.type = 'hidden';
                        inputDetalleId.name = `devoluciones[${index}][detalle_id]`;
                        inputDetalleId.value = detalleId;
                        form.appendChild(inputDetalleId);
                        
                        const inputCantidad = document.createElement('input');
                        inputCantidad.type = 'hidden';
                        inputCantidad.name = `devoluciones[${index}][cantidad]`;
                        inputCantidad.value = cantidad;
                        form.appendChild(inputCantidad);
                        
                        index++;
                    }
                }
                
                // Enviar el formulario después de adjuntar los campos ocultos
                form.submit();
            }
        };
    });
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
    `;
    document.head.appendChild(style);
});