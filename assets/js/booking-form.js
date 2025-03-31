(function($) {
    'use strict';
	
	 const hp = window.hp; 

    class BookingForm {
        constructor() {
            this.form = $('.bv-booking-form');
            this.isQuantityValid = true; // Nueva propiedad para tracking
            
            // Guardar la instancia en el elemento del form
            if (this.form.length) {
                this.form[0]._bookingFormInstance = this;
                window._bookingFormInstance = this;  // Backup global
            }
            
            const priceText = this.form.find('.bv-price-base span').text();
            this.basePrice = this.extractPrice(priceText);
            
            // Selectores
            this.dateBlock = this.form.find('.bv-datetime-row .bv-field-block:has(.bv-date-input)');
            this.dateInputs = this.form.find('.bv-date-input');
            this.quantityBlock = this.form.find('.bv-datetime-row .bv-field-block:has(.bv-quantity-input)');
            this.quantityInput = this.form.find('.bv-quantity-input');
            this.quantityValue = this.quantityBlock.find('.bv-block-value');
            this.bookButton = this.form.find('.bv-booking-button');
            
            // Extras
            this.extrasBlock = this.form.find('.bv-extras-row .bv-field-block');
            this.extrasDropdown = this.extrasBlock.find('.bv-extras-dropdown');
            this.extrasValue = this.extrasBlock.find('.bv-block-value');
            
            this.totalsContainer = this.form.find('.bv-totals-container');
 
            this.initEvents();
            this.updateBookButtonState(); // Verificar estado inicial
			this.updateExtrasText(); // Actualizar texto de extras seleccionados

			
			/*Event Quote Manager*/
			this.quoteButton = this.form.find('.eq-quote-button');
			this.listingId = this.quoteButton.data('listing-id');
			this.initQuoteButton();
        }

        initEvents() {
            this.initDatePickers();
            this.initQuantity();
            this.initExtras();
            this.calculateTotals();
 
            this.form.on('submit', (e) => {
                if (!this.isQuantityValid || !this.validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
		
		updateBookButtonState() {
            if (!this.isQuantityValid) {
                this.bookButton.prop('disabled', true);
            } else {
                this.bookButton.prop('disabled', false);
            }
        }

        initDatePickers() {
    const minLength = parseInt(this.dateInputs.first().data('min-length')) || 1;
    const maxLength = parseInt(this.dateInputs.first().data('max-length')) || 1;
    const bookingOffset = parseInt(this.dateInputs.first().data('booking-offset')) || 0;
    const bookingWindow = parseInt(this.dateInputs.first().data('booking-window')) || 365;
			
			// Cargar fecha desde localStorage si existe
const savedDate = localStorage.getItem('eq_selected_date');
    
    this.dateInputs.each((index, input) => {
        const $input = $(input);
        const $block = this.dateBlock;
        const $value = $block.find('.bv-block-value');
        
        let blockedDates = [];
        try {
            const rawData = $input.data('blocked-dates');
            blockedDates = typeof rawData === 'string' ? JSON.parse(rawData) : (rawData || []);

        } catch (e) {
            console.error('Error parsing blocked dates:', e);
        }

        const config = {
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(bookingOffset),
            maxDate: new Date().fp_incr(bookingWindow),
            appendTo: $block[0],
            positionElement: $block.find('.bv-field-content')[0],
            static: true,
            mode: maxLength > 1 ? "range" : "single",
            disable: blockedDates,
            onReady: (selectedDates, dateStr, instance) => {
    // Configuración inicial del picker
    instance.config.minRange = minLength;
    instance.config.maxRange = maxLength;
    
    // NUEVO: Verificar si debemos priorizar la fecha del Context Panel
    const isDateFromPanel = localStorage.getItem('eq_date_source') === 'panel';
    const canUseContextPanel = typeof eqContextData !== 'undefined' && eqContextData.canUseContextPanel;
    const panelDate = localStorage.getItem('eq_panel_selected_date');
    
   // Si la fecha viene del panel y el usuario tiene acceso, priorizar esta
    if (isDateFromPanel && canUseContextPanel && panelDate) {      
        
        // Verificar que realmente sea la fecha del panel y no del filtro
        const filterDate = localStorage.getItem('eq_selected_date');
        const dateSource = localStorage.getItem('eq_date_source');
        
        // Solo usar la fecha si realmente viene del panel
        if (dateSource === 'panel' && panelDate) {
            // Verificar si no está bloqueada
            const isBlocked = instance.config.disable.some(blocked => {
                if (typeof blocked === 'string') {
                    return blocked === panelDate;
                }
                return false;
            });
            
            if (!isBlocked) {
            // Fecha disponible del panel, usarla
            instance.setDate(panelDate, false);
            
            // CORRECCIÓN: Crear la fecha correctamente con la zona horaria local
            const parts = panelDate.split('-');
            if (parts.length === 3) {
                const year = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1; // Los meses en JS son 0-11
                const day = parseInt(parts[2]);
                
                // Crear fecha con hora fija a mediodía para evitar problemas de zonas horarias
                const panelDateObj = new Date(year, month, day, 12, 0, 0);
                
                const formattedDate = panelDateObj.toLocaleDateString('en-US', {
                    month: 'numeric',
                    day: 'numeric',
                    year: 'numeric'
                });
                
                $block.find('.bv-block-value').text(formattedDate);
                setTimeout(() => this.calculateTotals(), 100);
                
                // Mostrar mensaje informativo
                this.showNotification('Using date from Context Panel', 'info');
                
                return;
            }
        } else {
    // Si la fecha del panel está bloqueada:
    // 1. Mostrar advertencia
    this.showNotification('Date from Context Panel is not available for this listing', 'warning');
    
    // 2. NO usar esa fecha, dejar el campo vacío
    $block.find('.bv-block-value').text('Select');
    
    // 3. Limpiar cualquier fecha seleccionada anteriormente
    instance.clear();
    
    // 4. Opcional: Sugerir al usuario que seleccione una fecha manualmente
    setTimeout(() => {
        this.showNotification('Please select an available date for this listing', 'info');
    }, 1500);
}
        }
    }
        
    // Si no hay fecha del panel o no está disponible, seguir con el flujo normal
    // Verificar si hay fecha maestra en el carrito
    $.ajax({
        url: eqCartData.ajaxurl,
        type: 'POST',
        data: {
            action: 'eq_get_cart_master_date',
            nonce: eqCartData.nonce
        },
        success: (response) => {
            if (response.success && response.data.date) {
                const masterDate = response.data.date;
                
                // Verificar si la fecha maestra está disponible para este listing
                const isBlocked = instance.config.disable.some(blocked => {
                    if (typeof blocked === 'string') {
                        return blocked === masterDate;
                    }
                    return false;
                });
                
                if (!isBlocked) {
                    // Fecha disponible, usarla
                    instance.setDate(masterDate, false);
                    
                    // Actualizar el display
                    const masterDateObj = new Date(masterDate);
                    const formattedDate = masterDateObj.toLocaleDateString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    $block.find('.bv-block-value').text(formattedDate);
                    setTimeout(() => this.calculateTotals(), 100);
                    
                    // Mostrar mensaje informativo
                    this.showNotification('Using date from your existing quote items', 'info');
                } else {
                    // Si la fecha maestra no está disponible, usar la fecha guardada en localStorage
                    this.tryUseStoredDate(instance, $block);
                }
            } else {
                // Si no hay fecha maestra, intentar usar la del localStorage
                this.tryUseStoredDate(instance, $block);
            }
        },
        error: () => {
            // En caso de error, intentar usar la del localStorage
            this.tryUseStoredDate(instance, $block);
        }
    });
},
            // En la configuración del flatpickr, modificar el onChange
onChange: (selectedDates, dateStr, instance) => {
    if (selectedDates.length > 0) {
        if (maxLength === 1) {
            const date = selectedDates[0].toLocaleDateString('en-US', {
                month: 'numeric',
                day: 'numeric',
                year: 'numeric'
            });
            $value.text(date);
            // La primera fecha ya se actualiza automáticamente
        } 
        else if (selectedDates.length === 2) {
            const start = new Date(selectedDates[0]);
            const end = new Date(selectedDates[1]);
            start.setHours(0, 0, 0, 0);
            end.setHours(0, 0, 0, 0);
            
            // Calcular días incluyendo el día final
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            if (days < minLength || days > maxLength) {
                instance.clear();
                $value.text('Select');
                alert(`Please select a period between ${minLength} and ${maxLength} days`);
                return;
            }

            // Formatear fechas para mostrar
            const startDate = start.toLocaleDateString('en-US', {
                month: 'numeric',
                day: 'numeric'
            });
            const endDate = end.toLocaleDateString('en-US', {
                month: 'numeric',
                day: 'numeric',
                year: 'numeric'
            });
            $value.text(`${startDate} - ${endDate}`);

            // Actualizar los inputs hidden con formato YYYY-MM-DD
            this.dateInputs.eq(0).val(start.toISOString().split('T')[0]);
            this.dateInputs.eq(1).val(end.toISOString().split('T')[0]);
        }
        this.calculateTotals();
    }
	// Propagar cambio de fecha a otros componentes
if (selectedDates.length > 0) {
    const newDate = selectedDates[0].toISOString().split('T')[0];
    // Guardar en localStorage
    localStorage.setItem('eq_selected_date', newDate);
	
 $(document).on('eqDateChanged', (e, newDate, options) => {
    // Si tiene flag de forzar, actualizar sin importar el usuario
    if (options && options.force) {
        const dateInput = this.dateInputs.first();
        if (dateInput.length && dateInput[0]._flatpickr) {
            // Actualizar flatpickr
            dateInput[0]._flatpickr.setDate(newDate);
            
            // Actualizar también el display de texto
            const dateObj = new Date(newDate + 'T12:00:00');
            const formattedDate = dateObj.toLocaleDateString('en-US', {
                month: 'numeric',
                day: 'numeric',
                year: 'numeric'
            });
            this.dateBlock.find('.bv-block-value').text(formattedDate);
            
            // Recalcular totales después de un breve retraso
            setTimeout(() => this.calculateTotals(), 100);
                    }
    }
    // Si no tiene flag force, verificar origen y permisos
    else if (options && options.fromPanel) {
        const canUseContextPanel = typeof eqContextData !== 'undefined' && eqContextData.canUseContextPanel;
        if (canUseContextPanel) {
            const dateInput = this.dateInputs.first();
            if (dateInput.length && dateInput[0]._flatpickr) {
                // Verificar si la fecha está bloqueada
                const isBlocked = dateInput[0]._flatpickr.config.disable.some(blocked => {
                    if (typeof blocked === 'string') {
                        return blocked === newDate;
                    }
                    return false;
                });
                
                if (!isBlocked) {
                    // Actualizar flatpickr
                    dateInput[0]._flatpickr.setDate(newDate);
					
					const parts = newDate.split('-');
                if (parts.length === 3) {
                    const year = parseInt(parts[0]);
                    const month = parseInt(parts[1]) - 1; // Los meses en JS son 0-11
                    const day = parseInt(parts[2]);
                    
                    // Crear fecha con hora fija a mediodía para evitar problemas de zonas horarias
                    const dateObj = new Date(year, month, day, 12, 0, 0);
                    
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    
                    this.dateBlock.find('.bv-block-value').text(formattedDate);
                }
                    
                    // Recalcular totales después de un breve retraso
                    setTimeout(() => this.calculateTotals(), 100);
                    
                    // Mostrar indicación
                    this.showNotification('Date updated from Event Context Panel', 'info');
                    
                } else {
                    this.showNotification('Date from Context Panel is not available for this listing', 'warning');
                }
            }
        }
    }
});
	
}
}
        };

        const picker = flatpickr(input, config);
        
        if (index === 0) {
            $block.find('.bv-field-content').on('click', () => {
                if (picker && typeof picker.open === 'function') {
                    picker.open();
                }
            });
        }
    });
}
		initQuoteButton() {
    // Verificar dinámicamente si el ítem está en el carrito actual
    this.checkIfItemInCart();
    
    // Event listeners
    this.quoteButton.on('click', (e) => this.handleQuoteButtonClick(e));
    this.form.on('change', 'input, select', () => this.handleFormChange());
    
    // Actualizar cuando cambie el contexto
    $(document).on('eqContextDateChanged', () => this.checkIfItemInCart());
    
    // Escuchar evento personalizado cuando cambia el contexto
    $(document).on('eqContextChanged', () => {

        this.checkIfItemInCart();
    });
    
    // Variable para controlar si estamos editando
this.isEditing = false;

// Agregar listeners para detectar cuando el usuario está editando el formulario
this.form.find('input, select').on('change input', () => {
    this.isEditing = true;
    // Si estamos editando y el estado es "view", cambiarlo a "update"
    if (this.quoteButton.attr('data-quote-state') === 'view') {
        this.updateQuoteButtonState('update');
    }
});

// Verificar periódicamente (cada 5 segundos)
setInterval(() => {
    // Solo actualizar si NO estamos editando
    if (!this.isEditing) {
        this.checkIfItemInCart();
    }
}, 5000);
}

// Nueva función para verificar si el ítem está en el carrito actual
checkIfItemInCart() {
    if (!this.listingId) return;
    
    $.ajax({
        url: eqCartData.ajaxurl,
        type: 'POST',
        data: {
            action: 'eq_check_item_in_cart',
            nonce: eqCartData.nonce,
            listing_id: this.listingId
        },
        success: (response) => {
            if (response.success) {
                if (response.data.in_cart) {
                    this.updateQuoteButtonState('view');
                } else {
                    this.updateQuoteButtonState('add');
                    // Eliminar flag en localStorage para sincronizar estado
                    localStorage.removeItem(`quote_added_${this.listingId}`);
                }
            }
        },
        error: () => {
            console.error('Error checking if item is in cart');
        }
    });
}

   handleQuoteButtonClick(e) {
    e.preventDefault();
    const state = this.quoteButton.attr('data-quote-state');

    if (state === 'view') {
        // Redirigir a la página de cotización
        window.location.href = '/quote-cart'; // Ajustar según tu URL
        return;
    }

    if (!this.validateForm()) {
        this.showNotification('Please fill all required fields', 'error');
        return;
    }

    // Reiniciar flag de edición
    this.isEditing = false;
    this.addToQuote();
}

    handleFormChange() {
        if (this.quoteButton.attr('data-quote-state') === 'view') {
            this.updateQuoteButtonState('update');
        }
    }

    updateQuoteButtonState(state) {
        const states = {
            'add': '+ Quote',
            'view': 'View Quote',
            'update': 'Update Quote'
        };

        this.quoteButton
            .attr('data-quote-state', state)
            .text(states[state]);
    }

   async addToQuote() {
    const formData = this.collectFormData();
    const selectedDate = formData.date;

    try {
        // Verificar si hay panel de contexto activo y el usuario es privilegiado
        const canUseContextPanel = typeof eqContextData !== 'undefined' && eqContextData.canUseContextPanel;
        const isDateFromPanel = localStorage.getItem('eq_date_source') === 'panel';
        
        // Si el panel está activo y es administrador/ejecutivo, mostrar opciones avanzadas
        if (canUseContextPanel && isDateFromPanel) {
            const panelDate = localStorage.getItem('eq_panel_selected_date');
            
            // Si la fecha seleccionada es diferente a la fecha del panel, mostrar opciones
            if (selectedDate !== panelDate) {
                const action = await this.showEventDateOptions(selectedDate);
                
                if (action === 'cancel') {
                    return; // Usuario canceló la operación
                } else if (action === 'update_event') {
                    // Actualizar fecha del evento actual
                    await this.updateEventDate(selectedDate);
                } else if (action === 'duplicate_event') {
                    // Duplicar evento con nueva fecha
                    const transferItems = confirm("¿Desea transferir los items existentes al nuevo evento?");
                    await this.duplicateEvent(selectedDate, transferItems);
                } else if (action === 'new_event') {
                    // Abrir panel para crear nuevo evento
                    this.openCreateEventPanel(selectedDate);
                    return; // Detener el flujo hasta que se cree el evento
                }
            }
        }
        
        // Continuar con la validación normal del carrito
        const validateResponse = await $.ajax({
            url: eqCartData.ajaxurl,
            type: 'POST',
            data: {
                action: 'eq_validate_cart_date_change',
                nonce: eqCartData.nonce,
                date: selectedDate
            }
        });

        if (validateResponse.success) {
            // Si hay items en el carrito
if (validateResponse.data.hasItems) {
                // Si hay items no disponibles
    if (validateResponse.data.unavailableItems.length > 0) {
                    const confirmMessage = `Esta fecha afecta a ${validateResponse.data.unavailableItems.length} item(s) en tu carrito que no están disponibles en esta fecha:\n\n${validateResponse.data.unavailableItems.map(item => item.title).join('\n')}\n\n¿Quieres eliminar estos items y actualizar la fecha para el resto?`;
                    
                    if (confirm(confirmMessage)) {
                        // Actualizar fecha y eliminar items no disponibles
                        await $.ajax({
                            url: eqCartData.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'eq_update_cart_date',
                                nonce: eqCartData.nonce,
                                date: selectedDate,
                                remove_unavailable: 'true',
                                unavailable_ids: validateResponse.data.unavailableItems.map(item => item.id)
                            }
                        });
                    } else {
                        // Cancelar la operación
                        this.showNotification('Operación cancelada', 'info');
                        return;
                    }
    } else if (!validateResponse.data.allSameDate) {
		
		// Solo preguntar si las fechas son diferentes
        const confirmMessage = `Ya tienes ${validateResponse.data.itemCount} item(s) en tu carrito con una fecha diferente. ¿Quieres actualizar todos a la fecha ${selectedDate}?`;
        
                    
                    if (confirm(confirmMessage)) {
                        // Actualizar fecha para todos
                        await $.ajax({
                            url: eqCartData.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'eq_update_cart_date',
                                nonce: eqCartData.nonce,
                                date: selectedDate,
                                remove_unavailable: 'false',
                                unavailable_ids: []
                            }
                        });
                    } else {

                        // Cancelar la operación
                        this.showNotification('Operación cancelada', 'info');
                        return;
                    }
                }
            }
            
            // Ahora proceder con añadir al carrito
            const response = await $.ajax({
                url: eqCartData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'eq_add_to_cart',
                    nonce: eqCartData.nonce,
                    listing_id: this.listingId,
                    ...formData
                }
            });

            if (response.success) {
                localStorage.setItem(`quote_added_${this.listingId}`, 'true');
                this.updateQuoteButtonState('view');
                this.showNotification(response.data.message || 'Added to quote successfully');
            } else {
                this.showNotification(response.data || 'Error adding to quote', 'error');
            }
        } else {
            this.showNotification(validateResponse.data || 'Error validating date', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        this.showNotification('Error adding to quote', 'error');
    }
}
		
		// Mostrar opciones para manejar cambio de fecha del evento
showEventDateOptions(newDate) {
    // Primero verificar si ya existe un evento para esta fecha
    return $.ajax({
        url: eqCartData.ajaxurl,
        type: 'POST',
        data: {
            action: 'eq_check_event_exists',
            nonce: eqCartData.nonce,
            lead_id: this.getContextLeadId(),
            date: newDate
        }
    }).then(response => {
        if (response.success) {
            return new Promise((resolve) => {
                // Crear modal de opciones
                let optionsHtml = `
                    <div class="eq-modal-backdrop" id="eq-date-options-backdrop"></div>
                    <div class="eq-modal" id="eq-date-options-modal">
                        <div class="eq-modal-header">
                            <h3 class="eq-modal-title">Cambio de Fecha</h3>
                            <button type="button" class="eq-modal-close">&times;</button>
                        </div>
                        <div class="eq-modal-body">
                            <p>La fecha seleccionada (${newDate}) es diferente a la fecha del evento en el panel de contexto.</p>
                            <p>¿Qué desea hacer?</p>
                            <div class="eq-options-container">
                                <button type="button" class="eq-option-button update-event">Actualizar la fecha del evento actual</button>
                                <button type="button" class="eq-option-button duplicate-event">Duplicar el evento con la nueva fecha</button>
                                <button type="button" class="eq-option-button new-event">Crear un nuevo evento desde cero</button>`;
                
                // Agregar opción de usar evento existente si existe
                if (response.data.exists) {
                    optionsHtml += `
                                <button type="button" class="eq-option-button use-existing">Usar evento existente (${response.data.event_type})</button>`;
                }
                
                optionsHtml += `
                                <button type="button" class="eq-option-button cancel">Cancelar</button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Añadir modal al DOM
                $('body').append(optionsHtml);
                
                // Mostrar modal
                $('#eq-date-options-backdrop, #eq-date-options-modal').show();
                
                // Manejar acciones
                $('.eq-option-button.update-event').on('click', () => {
                    $('#eq-date-options-backdrop, #eq-date-options-modal').remove();
                    resolve('update_event');
                });
                
                $('.eq-option-button.duplicate-event').on('click', () => {
                    $('#eq-date-options-backdrop, #eq-date-options-modal').remove();
                    resolve('duplicate_event');
                });
                
                $('.eq-option-button.new-event').on('click', () => {
                    $('#eq-date-options-backdrop, #eq-date-options-modal').remove();
                    resolve('new_event');
                });
                
                if (response.data.exists) {
                    $('.eq-option-button.use-existing').on('click', () => {
                        $('#eq-date-options-backdrop, #eq-date-options-modal').remove();
                        resolve({
                            action: 'use_existing',
                            eventId: response.data.event_id
                        });
                    });
                }
                
                $('.eq-option-button.cancel, .eq-modal-close').on('click', () => {
                    $('#eq-date-options-backdrop, #eq-date-options-modal').remove();
                    resolve('cancel');
                });
            });
        } else {
            console.error('Error checking for existing events:', response.data);
            return this.showDefaultOptions(newDate);
        }
    }).catch(error => {
        console.error('AJAX error:', error);
        return this.showDefaultOptions(newDate);
    });
}
		
		// Método auxiliar para obtener el lead_id del contexto
getContextLeadId() {
    try {
        const contextData = sessionStorage.getItem('eqQuoteContext');
        if (contextData) {
            const parsedContext = JSON.parse(contextData);
            if (parsedContext.leadId) {
                return parsedContext.leadId;
            }
        }
    } catch (e) {
        console.error('Error reading context from sessionStorage', e);
    }
    return null;
}

// Actualizar la fecha del evento actual
async updateEventDate(newDate) {
    try {
        const response = await $.ajax({
            url: eqCartData.ajaxurl,
            type: 'POST',
            data: {
                action: 'eq_update_event_date',
                nonce: eqCartData.nonce,
                date: newDate
            }
        });
        
        if (response.success) {
            // Actualizar localStorage
            localStorage.setItem('eq_panel_selected_date', newDate);
            
            // Notificar al usuario
            this.showNotification('Fecha del evento actualizada correctamente', 'success');
            
            // Disparar evento de cambio de fecha
            $(document).trigger('eqContextDateChanged', [newDate]);
        } else {
            this.showNotification('Error al actualizar la fecha del evento', 'error');
        }
        
        return response.success;
    } catch (error) {
        console.error('Error updating event date:', error);
        this.showNotification('Error de conexión', 'error');
        return false;
    }
}

// Duplicar evento con nueva fecha
async duplicateEvent(newDate, transferItems) {
    try {
        const response = await $.ajax({
            url: eqCartData.ajaxurl,
            type: 'POST',
            data: {
                action: 'eq_duplicate_event',
                nonce: eqCartData.nonce,
                date: newDate,
                transfer_items: transferItems ? 'yes' : 'no'
            }
        });
        
        if (response.success) {
            // Actualizar localStorage
            localStorage.setItem('eq_panel_selected_date', newDate);
            
            // Notificar al usuario
            this.showNotification('Evento duplicado correctamente', 'success');
            
            // Recargar la página para mostrar el nuevo contexto
            window.location.reload();
        } else {
            this.showNotification('Error al duplicar el evento', 'error');
        }
        
        return response.success;
    } catch (error) {
        console.error('Error duplicating event:', error);
        this.showNotification('Error de conexión', 'error');
        return false;
    }
}

openCreateEventPanel(newDate) {
    
    // Simular clic en el botón "Cambiar Evento" del panel de contexto
    $('.eq-context-panel-button.change-event').click();
    
    // Esperar a que se abra el modal de eventos con un enfoque más robusto
    var attemptCount = 0;
    var maxAttempts = 10;
    
    var checkInterval = setInterval(function() {
        var eventModal = $('#eq-event-modal');
        attemptCount++;
        
        if (eventModal.is(':visible')) {
            clearInterval(checkInterval);
            
            // Seleccionar la pestaña "Crear nuevo evento"
            var newEventTab = eventModal.find('.eq-modal-tab[data-tab="new"]');
            if (newEventTab.length) {
                newEventTab.click();
            }
            
            // Establecer la fecha en el campo con más comprobaciones
            var dateInput = eventModal.find('#eq-new-event-date');
            
            if (dateInput.length) {
                // Verificar si flatpickr está inicializado
                if (dateInput[0]._flatpickr) {

                    dateInput[0]._flatpickr.setDate(newDate);
                } else {

                    dateInput.val(newDate);
                }
                
                // Forzar un evento change para asegurar que otros handlers lo capten
                dateInput.trigger('change');
                
            } else {
                console.error('Date input field not found in event modal');
            }
        } else if (attemptCount >= maxAttempts) {
            clearInterval(checkInterval);
            console.error('Event modal did not appear after maximum attempts');
        }
    }, 200); // Incrementado el intervalo para dar más tiempo
}

    collectFormData() {
    const extras = [];
    
    // Obtener extras variables
    this.extrasDropdown.find('.bv-extra-quantity').each((_, input) => {
        const $input = $(input);
        const extraQuantity = parseInt($input.val()) || 0;
        
        if (extraQuantity > 0) {
            extras.push({
                id: $input.data('index'),
                type: 'variable_quantity',
                name: $input.data('name'),
                price: $input.data('price'),
                quantity: extraQuantity
            });
        }
    });

    // Obtener extras normales
    this.extrasDropdown.find('input[type="checkbox"]:checked').each((_, checkbox) => {
        const $checkbox = $(checkbox);
        extras.push({
            id: $checkbox.val(),
            type: $checkbox.data('type'),
            name: $checkbox.data('name'),
            price: $checkbox.data('price'),
            quantity: 1
        });
    });

    return {
        date: this.dateInputs.first().val(),
        quantity: this.quantityInput.val(),
        extras: extras,
        calculated_price: this.form.find('input[name="_calculated_price"]').val()
    };
}

    showNotification(message, type = 'success') {
        const notification = $(`
            <div class="eq-notification ${type}">
                ${message}
            </div>
        `).appendTo('body');

        setTimeout(() => notification.addClass('show'), 100);
        setTimeout(() => {
            notification.removeClass('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

		
        initQuantity() {
            this.quantityBlock.find('.bv-field-content').on('click', () => {
                const currentValue = parseInt(this.quantityInput.val()) || 1;
                const minQuantity = parseInt(this.quantityInput.attr('min'));
                const maxQuantity = parseInt(this.quantityInput.attr('max'));
                const $temp = $('<input type="number" class="temp-quantity" min="1">');
                
                $temp.val(currentValue)
                     .css({
                         width: '100%',
                         padding: '5px',
                         border: '1px solid #cbb881',
                         borderRadius: '4px'
                     });
 
                this.quantityValue.html($temp);
                $temp.focus().select();
 
                const handleValidation = (newValue) => {
                    this.isQuantityValid = (newValue >= minQuantity && newValue <= maxQuantity);
                    
                    if (!this.isQuantityValid) {
                        $temp.addClass('invalid');
                        if (!$temp.next('.error-message').length) {
                            $temp.after(`<div class="error-message">Please enter a value between ${minQuantity} and ${maxQuantity}</div>`);
                        }
                    } else {
                        $temp.removeClass('invalid');
                        $temp.next('.error-message').remove();
                    }
                    
                    this.updateBookButtonState();
                    return this.isQuantityValid;
                };
 
                const handleChange = () => {
                    let newValue = parseInt($temp.val()) || 1;
                    if (handleValidation(newValue)) {
                        this.quantityInput.val(newValue);
                        this.quantityValue.text(newValue);
                        this.calculateTotals();
                    }
                };
 
                $temp.on('input', function() {
                    let newValue = parseInt($(this).val()) || 1;
                    handleValidation(newValue);
                });
 
                $temp.on('blur', handleChange);
                $temp.on('keypress', (e) => {
                    if (e.which === 13) { // Enter
                        handleChange();
                        if (this.isQuantityValid) {
                            $temp.blur();
                        }
                    }
                });
            });
        }
		
		
        initExtras() {
            // Toggle del dropdown
            this.extrasBlock.find('.bv-field-content').on('click', (e) => {
                e.stopPropagation();
                if (this.extrasDropdown.is(':visible')) {
                    this.hideExtrasDropdown();
                } else {
                    this.showExtrasDropdown();
                }
            });

            // Prevenir cierre al hacer click dentro del dropdown
            this.extrasDropdown.on('click', (e) => {
                e.stopPropagation();
            });

            // Manejar cambios en checkboxes
            this.extrasDropdown.on('change', 'input[type="checkbox"]', () => {
                this.updateExtrasText();
                this.calculateTotals();
            });

            // Manejar cambios en inputs variables
 this.extrasDropdown.on('input', '.bv-extra-quantity', () => {
        this.updateExtrasText();
        this.calculateTotals();
    });


            // Cerrar al hacer click fuera
            $(document).on('click', (e) => {
                if (!this.extrasBlock.is(e.target) && 
                    !this.extrasBlock.has(e.target).length) {
                    this.hideExtrasDropdown();
                }
            });
        }
		
	showExtrasDropdown() {
            this.extrasBlock.addClass('active');
            this.extrasDropdown.slideDown(200);
        }
		

        toggleExtrasDropdown() {
            this.extrasDropdown.slideToggle(200);
            this.extrasBlock.toggleClass('active');
        }

        hideExtrasDropdown() {
            this.extrasBlock.removeClass('active');
            this.extrasDropdown.slideUp(200);
        }

        updateExtrasText() {
            const checkedCount = this.extrasDropdown.find('input[type="checkbox"]:checked').length;
            const variableCount = this.extrasDropdown.find('.bv-extra-quantity').filter(function() {
                return parseInt($(this).val()) > 0;
            }).length;
            
            const totalCount = checkedCount + variableCount;
            this.extrasValue.text(totalCount > 0 ? `${totalCount} selected` : 'Select');
        }
		
		tryUseStoredDate(instance, $block) {
    // Verificar si debemos priorizar la fecha del panel
    const isDateFromPanel = localStorage.getItem('eq_date_source') === 'panel';
    const canUseContextPanel = typeof eqContextData !== 'undefined' && eqContextData.canUseContextPanel;
    
    // Si viene del panel y el usuario tiene acceso, intentar usar esa primero
    if (isDateFromPanel && canUseContextPanel) {
        const panelDate = localStorage.getItem('eq_panel_selected_date');
        if (panelDate) {
            const isBlocked = instance.config.disable.some(blocked => {
                if (typeof blocked === 'string') {
                    return blocked === panelDate;
                }
                return false;
            });
            
            if (!isBlocked) {
                // Fecha disponible, usarla
                instance.setDate(panelDate, false);
                
                // CORRECCIÓN: Crear la fecha correctamente con la zona horaria local
                const parts = panelDate.split('-');
                if (parts.length === 3) {
                    const year = parseInt(parts[0]);
                    const month = parseInt(parts[1]) - 1; // Los meses en JS son 0-11
                    const day = parseInt(parts[2]);
                    
                    // Crear fecha con hora fija a mediodía para evitar problemas de zonas horarias
                    const panelDateObj = new Date(year, month, day, 12, 0, 0);
                    
                    const formattedDate = panelDateObj.toLocaleDateString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    
                    $block.find('.bv-block-value').text(formattedDate);
                    setTimeout(() => this.calculateTotals(), 100);
                    
                    this.showNotification('Using date from Context Panel', 'info');
                    return;
                }
            }
        }
    }
    
    // Si no hay fecha del panel o no está disponible, usar la fecha normal de localStorage
    const savedDate = localStorage.getItem('eq_selected_date');
    if (savedDate) {
        // Intentar usar la fecha guardada en localStorage
        const parts = savedDate.split('-');
        if (parts.length === 3) {
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]);
            
            const savedDateObj = new Date(year, month, day);
            
            const isBlocked = instance.config.disable.some(blocked => {
                if (typeof blocked === 'string') {
                    return blocked === savedDate;
                }
                return false;
            });
            
            if (!isBlocked) {
                instance.setDate(savedDate, false);
                
                const formattedDate = savedDateObj.toLocaleDateString('en-US', {
                    month: 'numeric',
                    day: 'numeric',
                    year: 'numeric'
                });
                $block.find('.bv-block-value').text(formattedDate);
                setTimeout(() => this.calculateTotals(), 100);
            }
        }
    }
}
		
		useDateFromPanel(instance, $block, panelDate) {
    // Verificar si la fecha está bloqueada
    const isBlocked = instance.config.disable.some(blocked => {
        if (typeof blocked === 'string') {
            return blocked === panelDate;
        }
        return false;
    });
    
    if (!isBlocked) {
        instance.setDate(panelDate, false);
        
        // Actualizar el display
        const dateObj = new Date(panelDate + 'T12:00:00'); // Agregar mediodía para evitar problemas de zona horaria
        const formattedDate = dateObj.toLocaleDateString('en-US', {
            month: 'numeric',
            day: 'numeric',
            year: 'numeric'
        });
        $block.find('.bv-block-value').text(formattedDate);
        setTimeout(() => this.calculateTotals(), 100);
        
        // Mostrar mensaje informativo
        this.showNotification('Using date from Event Context Panel', 'info');
    }
}

        calculateTotals() {
    const totals = [];
    let subTotal = 0;
    const quantity = parseInt(this.quantityInput.val()) || 1;
    const days = this.calculateDays();

// Base price calculation
const dates = this.dateInputs.map((_, input) => input.value).get();
let baseTotal = this.basePrice * quantity; // Iniciamos con precio base * quantity

if (dates[0]) {
    // Si hay al menos una fecha seleccionada
    const start = new Date(dates[0] + 'T00:00:00');
    // Si hay segunda fecha usar esa, sino usar la primera
    const end = dates[1] ? new Date(dates[1] + 'T00:00:00') : new Date(start);


    // Calcular día por día
    baseTotal = 0; // Aquí sí reiniciamos a 0 porque vamos a calcular todo
    const currentDate = new Date(start);
    while (currentDate <= end) {
        const dayPrice = this.getDayPrice(currentDate);
        baseTotal += dayPrice * quantity;
        currentDate.setDate(currentDate.getDate() + 1);
    }
}

totals.push({
    label: `Base price ${quantity > 1 ? `× ${quantity}` : ''} ${days > 1 ? `× ${days} days` : ''}`,
    amount: baseTotal
});
subTotal += baseTotal;

    // Variable extras
    this.extrasDropdown.find('.bv-extra-quantity').each((_, input) => {
        const $input = $(input);
        const extraQuantity = parseInt($input.val()) || 0;
        
        if (extraQuantity > 0) {
            const price = parseFloat($input.data('price')) || 0;
            const name = $input.data('name');
            const total = price * extraQuantity;
            
            totals.push({
                label: `${name} × ${extraQuantity}`,
                amount: total
            });
            subTotal += total;
        }
    });

 // Regular extras con logs y nueva lógica
    this.extrasDropdown.find('input[type="checkbox"]:checked').each((_, checkbox) => {
        const $checkbox = $(checkbox);
        const price = parseFloat($checkbox.data('price')) || 0;
        const name = $checkbox.data('name');
        const rawType = checkbox.getAttribute('data-type'); // Cambio aquí
        const quantity = parseInt(this.quantityInput.val()) || 1;

        let extraTotal = price;
        let label = name;

        if (rawType === 'per_item') {
            extraTotal *= days;
            label += ` × ${days} days`;
        } 
        else if (!rawType || rawType === '') {
            if (days > 0) {
                extraTotal = price * days * quantity;
                label += ` × ${quantity} × ${days} days`;
            }
        } 
        else if (rawType === 'per_quantity') {
            extraTotal = price * quantity;
            label += ` × ${quantity}`;
        }
        else if (rawType === 'per_order') {
            extraTotal = price;
            // No multiplicadores para per_order
        }

        totals.push({ label, amount: extraTotal });
        subTotal += extraTotal;
    });

    // Calcular taxes usando hp_vendor_commission_rate
		const taxRate = parseFloat(bvData.tax_rate) || 0;
		const taxes = subTotal * (taxRate / 100);

    const grandTotal = subTotal + taxes;


    // Actualizar formulario y mostrar totales
    this.form.find('input[name="_calculated_price"]').val(grandTotal);
			
		// Agregar:
const priceDetails = {
    extras: totals.map(item => ({
        name: item.label,
        amount: item.amount
    }))
};

// Agregar campo hidden si no existe
if (!this.form.find('input[name="price_details"]').length) {
    this.form.append($('<input>', {
        type: 'hidden',
        name: 'price_details'
    }));
}

// Guardar los detalles
this.form.find('input[name="price_details"]').val(JSON.stringify(priceDetails));
			
    this.renderTotals(totals, grandTotal);
}

        calculateDays() {
    const maxLength = parseInt(this.dateInputs.first().data('max-length')) || 1;
    const minLength = parseInt(this.dateInputs.first().data('min-length')) || 1;
    const dates = this.dateInputs.map((_, input) => input.value).get();
    
    // Para fecha única
    if (maxLength === 1) {
        return dates[0] ? 1 : 0;
    }
    
    // Para rango de fechas
    if (dates.length === 2 && dates[0] && dates[1]) {
        const start = new Date(dates[0]);
        const end = new Date(dates[1]);
        start.setHours(0, 0, 0, 0);
        end.setHours(0, 0, 0, 0);
        
        // Incluir día final en el cálculo
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        return days;
    }
    
    // Si hay al menos una fecha seleccionada
    if (dates[0]) {
        return 1;
    }
    
    return 0;
}

        extractPrice(priceString) {
            const matches = priceString.match(/[\d,]+(\.\d{2})?/);
            return matches ? parseFloat(matches[0].replace(/,/g, '')) : 0;
        }
		
		getDayPrice(date) {
    try {
        const ranges = this.dateInputs.first().data('booking-ranges');
        if (!ranges || !Array.isArray(ranges)) {
            return this.basePrice;
        }

        const dayOfWeek = date.getDay();
        let maxPrice = this.basePrice;

        ranges.forEach((range, index) => {
            if (range && Array.isArray(range.days) && range.days.includes(dayOfWeek)) {
                const rangePrice = parseFloat(range.price);
                if (!isNaN(rangePrice)) {
                    maxPrice = rangePrice;

                }
            }
        });

        return maxPrice;
    } catch (error) {

        return this.basePrice;
    }
}

        validateForm() {
    let isValid = true;
    const messages = [];

    // Validar fechas
    this.dateInputs.each(function() {
        if (!this.value) {
            isValid = false;
            messages.push('Please select the required dates');
            return false;
        }
    });

    // Validar cantidad
    const value = parseInt(this.quantityInput.val()) || 0;
    const minQuantity = parseInt(this.quantityInput.attr('min'));
    const maxQuantity = parseInt(this.quantityInput.attr('max'));

    if (value < minQuantity || value > maxQuantity) {
        isValid = false;
        messages.push(`Please enter a value between ${minQuantity} and ${maxQuantity}`);
    }

    if (!isValid) {
        alert(messages[0]);
    }

    return isValid;
}

        renderTotals(items, total) {
    if (!this.totalsContainer.length) {
        console.error('Totals container not found');
        return;
    }

    let html = '';

    // Items individuales
    items.forEach(item => {
        html += `
            <div class="bv-total-item">
                <span>${item.label}</span>
                <span>${this.formatPrice(item.amount)}</span>
            </div>
        `;
    });

    // Total final con taxes incluidos
    html += `
        <div class="bv-total-final">
            <span>Total (incl. taxes)</span>
            <span>${this.formatPrice(total)}</span>
        </div>
    `;

    this.totalsContainer.html(html);
    this.totalsContainer.show();
}

        formatPrice(amount) {
    // Asegurar que amount sea un número
    amount = parseFloat(amount);
    
    // Formatear con el número correcto de decimales
    let formattedPrice = amount.toFixed(bvData.num_decimals);
    
    // Dividir en parte entera y decimal
    let parts = formattedPrice.split('.');
    
    // Agregar separadores de miles a la parte entera
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, bvData.thousand_sep);
    
    // Juntar con el separador decimal correcto
    formattedPrice = parts.join(bvData.decimal_sep);
    
    // Agregar el símbolo de moneda
    return bvData.currency + formattedPrice;
}
    }
	
	// Función auxiliar para verificar si el quantity está presente
function hasQuantityField() {
    return $('.bv-datetime-row').not('.no-quantity').length > 0;
}

   // Hacer disponible la clase globalmente para el popup
    window.BookingForm = BookingForm;

    // Mantener la inicialización automática para la página de listing
    if (document.querySelector('.bv-booking-form')) {
        $(document).ready(() => {
            const bookingForm = new BookingForm();
        });
    }


})(jQuery);