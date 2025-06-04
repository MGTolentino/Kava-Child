(function($) {
    'use strict';

    const isMobile = () => window.innerWidth <= 768;

    class MobileBooking {
        constructor() {
    // Verificar si estamos en móvil y si el form existe
    if (!isMobile() || !$('.bv-booking-form').length) return;

    // Referencias al form original y BookingForm
    this.originalForm = $('.bv-booking-form');
    this.bookingFormInstance = null;
    this.findBookingFormInstance();

    // Referencias al form original
			const basePrice = this.extractPrice(this.originalForm.find('.bv-price-base span').text());
	const taxRate = parseFloat(bvData.tax_rate) || 0;
	const priceWithTax = basePrice + (basePrice * (taxRate / 100));
	this.priceBase = this.formatPrice(priceWithTax);
    this.originalDateInputs = this.originalForm.find('.bv-date-input');
    this.totalContainer = this.originalForm.find('.bv-totals-container');
    this.quantityInput = this.originalForm.find('.bv-quantity-input');
    this.quantityBlock = this.originalForm.find('.bv-datetime-row .bv-field-block:has(.bv-quantity-input)');
    this.extrasContainer = this.originalForm.find('.bv-extras-dropdown');
    this.formExtras = $('.bv-extras-dropdown');
    
    // Verificación para mostrar quantity
    this.shouldShowQuantity = !this.originalForm.find('.bv-datetime-row').hasClass('no-quantity');
    
    // Estado móvil
    this.selectedDates = [];
    this.currentTotal = this.priceBase;
    
    this.createMobileElements(); // Primero crear los elementos
    this.initExtras();  // ← Moverlo aquí después de crear el modal
    this.init();
    this.initEvents();
    
    this.originalForm[0]._bookingForm = this;
}
		
		extractPrice(priceString) {
    const matches = priceString.match(/[\d,]+(\.\d{2})?/);
    return matches ? parseFloat(matches[0].replace(/,/g, '')) : 0;
}

formatPrice(amount) {
    amount = parseFloat(amount);
    let formattedPrice = amount.toFixed(bvData.num_decimals);
    let parts = formattedPrice.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, bvData.thousand_sep);
    formattedPrice = parts.join(bvData.decimal_sep);
    return bvData.currency + formattedPrice;
}
		
        findBookingFormInstance() {
            // Intentar obtener la instancia directamente
            if (this.originalForm[0] && this.originalForm[0]._bookingFormInstance) {
                this.bookingFormInstance = this.originalForm[0]._bookingFormInstance;
                return;
            }

            // Intentar obtener del backup global
            if (window._bookingFormInstance) {
                this.bookingFormInstance = window._bookingFormInstance;
                return;
            }

            // Si no está disponible, observar cambios
            const observer = new MutationObserver((mutations, obs) => {
                if (this.originalForm[0]._bookingFormInstance || window._bookingFormInstance) {
                    this.bookingFormInstance = this.originalForm[0]._bookingFormInstance || window._bookingFormInstance;
                    obs.disconnect();
                }
            });

            observer.observe(this.originalForm[0], {
                attributes: true,
                childList: true,
                subtree: true
            });
        }

        createMobileElements() {
            // Footer HTML
            const footerHtml = `
                <div class="bv-mobile-booking-footer">
                    <div class="bv-mobile-booking-info">
                        <div class="bv-mobile-price-info">
                            <div class="bv-mobile-price">${this.priceBase}</div>
                            <div class="bv-mobile-dates">Select dates</div>
                        </div>
                        <div class="bv-mobile-button-container">
                            <button class="bv-mobile-book-button">Book Now</button>
                        </div>
                    </div>
                </div>
            `;

            // Modal HTML
            // En createMobileElements(), modificar el modalHtml para seguir el mismo patrón:
const modalHtml = `
   <div class="bv-mobile-booking-modal">
       <div class="bv-mobile-modal-content">
           <div class="bv-mobile-modal-header">
               <span class="bv-mobile-modal-title">Price Details</span>
               <button class="bv-mobile-modal-close">&times;</button>
           </div>
           <div class="bv-mobile-modal-body">
               <div class="bv-mobile-price-details"></div>
               
               ${this.shouldShowQuantity ? `
               <div class="bv-mobile-quantity-section">
                   <span class="bv-mobile-quantity-label">Quantity</span>
                   <input type="number" class="bv-mobile-quantity-input" min="1" value="1">
               </div>
               ` : ''}

               <div class="bv-mobile-dates-section">
                   <div class="bv-mobile-dates-info">
                       <span>Dates</span>
                       <span class="bv-mobile-selected-dates">Select dates</span>
                   </div>
                   <input type="hidden" class="bv-mobile-date-start">
                   <input type="hidden" class="bv-mobile-date-end">
                   <button class="bv-mobile-change-dates">Select dates</button>
               </div>

               <!-- Sección de extras siguiendo el mismo patrón -->
               <div class="bv-mobile-extras-section">
                   <div class="bv-mobile-extras-info">
                       <span>Extras</span>
                       <span class="bv-mobile-selected-extras">Select</span>
                   </div>
                   <div class="bv-mobile-extras-content">
                       <!-- Se llenará dinámicamente con los extras -->
                   </div>
               </div>

               <button class="bv-mobile-modal-book" disabled>Book Now</button>
           </div>
       </div>
   </div>
`;
			
		

            // Agregar al DOM
            $('body').append(footerHtml);
            $('body').append(modalHtml);


           // Guardar referencias
    this.footer = $('.bv-mobile-booking-footer');
    this.modal = $('.bv-mobile-booking-modal');
    this.priceDetails = this.modal.find('.bv-mobile-price-details');
    this.mobileStartInput = this.modal.find('.bv-mobile-date-start');
    this.mobileEndInput = this.modal.find('.bv-mobile-date-end');
}

        init() {
            this.initializeCalendars();
        }

        initializeCalendars() {
    const isRange = this.originalDateInputs.length > 1;
    const firstInput = this.originalDateInputs.first();
    
    const minLength = parseInt(firstInput.data('min-length')) || 1;
    const maxLength = parseInt(firstInput.data('max-length')) || 1;
    const bookingOffset = parseInt(firstInput.data('booking-offset')) || 0;
    const bookingWindow = parseInt(firstInput.data('booking-window')) || 365;
    
    const blockedDates = firstInput.data('blocked-dates') || [];
    
    const options = {
        enableTime: false,
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(bookingOffset),
        maxDate: new Date().fp_incr(bookingWindow),
        disableMobile: true,
        disable: blockedDates,
        mode: maxLength === 1 ? "single" : "range",
        onReady: (selectedDates, dateStr, instance) => {
            instance.config.minRange = minLength;
            instance.config.maxRange = maxLength;
        },
onChange: (selectedDates) => {
   if (selectedDates.length > 0) {
       if (maxLength === 1) {
           // Para selección única, sincronizar antes de la validación
           this.handleDateSelection([selectedDates[0]]);
           // Actualizar input original y forzar validación después de la sincronización
           this.syncWithOriginalForm();
       } else if (selectedDates.length === 2) {
           // Para rango de fechas
           const start = new Date(selectedDates[0]);
           const end = new Date(selectedDates[1]);
           start.setHours(0, 0, 0, 0);
           end.setHours(0, 0, 0, 0);
           
           const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
           
           if (days < minLength || days > maxLength) {
               if (this.mobilePicker) {
                   this.mobilePicker.clear();
               }
               alert(`Please select a period between ${minLength} and ${maxLength} days`);
               return;
           }
           this.handleDateSelection(selectedDates);
       }
   }
}
    };

    if (this.mobileStartInput && this.mobileStartInput.length) {
        this.mobilePicker = flatpickr(this.mobileStartInput[0], options);
    }
}


        initEvents() {
    // Click en footer abre modal
    this.footer.on('click', () => {
        this.openModal();
    });

    // Cerrar modal
    this.modal.find('.bv-mobile-modal-close').on('click', (e) => {
        e.stopPropagation();
        this.closeModal();
    });

    // Click fuera del modal cierra
    this.modal.on('click', (e) => {
        if ($(e.target).is(this.modal)) {
            this.closeModal();
        }
    });

    // Botón de cambiar fechas
    this.modal.find('.bv-mobile-change-dates').on('click', (e) => {
        e.stopPropagation();
        if (this.mobilePicker) {
            this.mobilePicker.open();
        }
    });

    // Manejo de cantidad si está habilitado
    if (this.shouldShowQuantity) {
        const $quantityInput = this.modal.find('.bv-mobile-quantity-input');
        const originalValue = this.quantityInput.val();
        const minQuantity = parseInt(this.quantityInput.attr('min'));
        const maxQuantity = parseInt(this.quantityInput.attr('max'));
        
        $quantityInput.val(originalValue);

        $quantityInput.on('input change keypress', (e) => {
            // Si es keypress y no es Enter, salir
            if (e.type === 'keypress' && e.which !== 13) return;
            
            let value = parseInt($(e.target).val()) || 1;
            
            // Validar rango
            if (value < minQuantity || value > maxQuantity) {
                $(e.target).addClass('invalid');
                if (!$(e.target).next('.error-message').length) {
                    $(e.target).after(`<div class="error-message">Please enter a value between ${minQuantity} and ${maxQuantity}</div>`);
                }
            } else {
                $(e.target).removeClass('invalid');
                $(e.target).next('.error-message').remove();
            }

            // Actualizar input y valor visible en el form original
            this.quantityInput.val(value);
            this.quantityBlock.find('.bv-block-value').text(value);
            
            // Forzar recálculo en el form principal
            if (this.bookingFormInstance && typeof this.bookingFormInstance.calculateTotals === 'function') {
                this.bookingFormInstance.calculateTotals();
            } else {
                // Intentar reconectar con el form principal
                this.findBookingFormInstance();
                if (this.bookingFormInstance) {
                    this.bookingFormInstance.calculateTotals();
                }
            }
        });
    }

    // Botón de reserva
    // En initEvents() o donde se manejan los eventos del modal
this.modal.find('.bv-mobile-modal-book').on('click', (e) => {
    e.preventDefault();
    
    // Validar cantidad si está habilitada
    if (this.shouldShowQuantity) {
        const value = parseInt(this.quantityInput.val()) || 0;
        const minQuantity = parseInt(this.quantityInput.attr('min'));
        const maxQuantity = parseInt(this.quantityInput.attr('max'));

        if (value < minQuantity || value > maxQuantity) {
            alert(`Please enter a value between ${minQuantity} and ${maxQuantity}`);
            return;
        }
    }

    // Validar que haya fechas seleccionadas
    if (!this.selectedDates || !this.selectedDates.length) {
        alert('Please select a date');
        return;
    }

    // Validar booking offset y window
    const bookingOffset = parseInt(this.originalDateInputs.first().data('booking-offset')) || 0;
    const bookingWindow = parseInt(this.originalDateInputs.first().data('booking-window')) || 365;
    
    // Si todo es válido, cerrar modal y continuar
    this.closeModal();
    $('.bv-booking-form .bv-booking-button').click();
});

    // Observer para cambios en totales
    const observer = new MutationObserver(() => {
        this.updatePrices();
    });

    observer.observe(this.totalContainer[0], {
        childList: true,
        subtree: true,
        characterData: true
    });
			
}
		
		initExtras() {
    const $extrasSection = this.modal.find('.bv-mobile-extras-section');
    const $extrasInfo = $extrasSection.find('.bv-mobile-extras-info');
    const $extrasContent = $extrasSection.find('.bv-mobile-extras-content');
    const $selectedExtras = $extrasInfo.find('.bv-mobile-selected-extras');
 
    // Asegurar que el contenido está oculto inicialmente
    $extrasContent.hide();
 
    // Toggle mejorado del contenido
    $extrasInfo.on('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        $extrasContent.slideToggle(200);
        $extrasInfo.toggleClass('active');
    });
 
    if (this.formExtras.length) {
        $extrasContent.html(this.formExtras.html());
 
        // Checkboxes
        $extrasContent.on('change', 'input[type="checkbox"]', (e) => {
            const $checkbox = $(e.target);
            this.formExtras.find(`input[value="${$checkbox.val()}"]`)
                .prop('checked', $checkbox.prop('checked'))
                .trigger('change');
            
            this.syncExtrasState($selectedExtras);
        });
 
        // Inputs variables
        $extrasContent.on('input', '.bv-extra-quantity', (e) => {
            const $input = $(e.target);
            const value = parseInt($input.val()) || 0;
            const $formInput = this.formExtras.find(`.bv-extra-quantity[data-name="${$input.data('name')}"]`);
            
            $formInput.val(value).trigger('change');
            
            if (value === 0) {
                $input.val('');
            }
			
			// Forzar recálculo en el form principal
if (this.bookingFormInstance && typeof this.bookingFormInstance.calculateTotals === 'function') {
    this.bookingFormInstance.calculateTotals();
} else {
    // Intentar reconectar con el form principal
    this.findBookingFormInstance();
    if (this.bookingFormInstance) {
        this.bookingFormInstance.calculateTotals();
    }
}
            
            this.syncExtrasState($selectedExtras);
        });
 
        // Observar cambios en el form y tarjetas
        const observer = new MutationObserver(() => {
            this.syncExtrasState($selectedExtras);
        });
 
        // Observar tanto el form como el contenedor de totales
        observer.observe(this.formExtras[0], {
            subtree: true,
            childList: true,
            characterData: true,
            attributes: true
        });
 
        observer.observe(this.totalContainer[0], {
            subtree: true,
            childList: true,
            characterData: true
        });
 
        // Sincronización inicial
        this.syncExtrasState($selectedExtras);
    }
 
    // Cerrar dropdown al hacer click fuera
    $(document).on('click', (e) => {
        if (!$extrasSection.is(e.target) && 
            !$extrasSection.has(e.target).length) {
            $extrasContent.slideUp(200);
            $extrasInfo.removeClass('active');
        }
    });
 }
		
		 syncExtrasState($selectedExtras) {
    // Sincronizar checkboxes
    this.formExtras.find('input[type="checkbox"]').each((_, checkbox) => {
        const $checkbox = $(checkbox);
        const $mobileCheckbox = this.modal.find(`input[value="${$checkbox.val()}"]`);
        $mobileCheckbox.prop('checked', $checkbox.prop('checked'));
    });
 
    // Sincronizar inputs variables
    this.formExtras.find('.bv-extra-quantity').each((_, input) => {
        const $input = $(input);
        const value = parseInt($input.val()) || 0;
        const $mobileInput = this.modal.find(`.bv-extra-quantity[data-name="${$input.data('name')}"]`);
        
        if (value === 0) {
            $mobileInput.val('');
        } else {
            $mobileInput.val(value);
        }
    });
 
    // Actualizar contador
    const checkedCount = this.formExtras.find('input[type="checkbox"]:checked').length;
    const variableCount = this.formExtras.find('.bv-extra-quantity').filter(function() {
        return parseInt($(this).val()) > 0;
    }).length;
    
    const totalCount = checkedCount + variableCount;
    $selectedExtras.text(totalCount > 0 ? `${totalCount} selected` : 'Select');
 }
		
		updateExtrasCount($element) {
    const checkedCount = this.formExtras.find('input[type="checkbox"]:checked').length;
    const variableCount = this.formExtras.find('.bv-extra-quantity').filter(function() {
        return parseInt($(this).val()) > 0;
    }).length;
    
    const totalCount = checkedCount + variableCount;
    $element.text(totalCount > 0 ? `${totalCount} selected` : 'Select');
 }
		
		toggleExtrasDropdown() {
    this.extrasDropdown.slideToggle(200);
    this.extrasHeader.toggleClass('active');
 }
 
 hideExtrasDropdown() {
    this.extrasDropdown.slideUp(200);
    this.extrasHeader.removeClass('active');
 }
 
 updateExtrasText() {
    const checkedCount = this.extrasDropdown.find('input[type="checkbox"]:checked').length;
    const variableCount = this.extrasDropdown.find('.bv-extra-quantity').filter(function() {
        return parseInt($(this).val()) > 0;
    }).length;
    
    const totalCount = checkedCount + variableCount;
    this.extrasSelected.text(totalCount > 0 ? `${totalCount} selected` : 'Select');
 }

    handleDateSelection(selectedDates) {
    if (selectedDates && selectedDates.length > 0) {
        this.selectedDates = selectedDates.map(date => {
            date.setHours(0, 0, 0, 0);
            return {
                display: date.toLocaleDateString(),
                iso: date.toISOString().split('T')[0]
            };
        });
        
        // Actualizar UI primero
        this.updateDatesDisplay();
        
        // Sincronizar con form original y forzar recálculo
        this.syncWithOriginalForm();
        
        // Actualizar estado del botón
        this.updateBookButtonState();
    }
}

        updateBookButtonState() {
            const hasSelectedDates = this.selectedDates.length > 0;
            const mobileButton = this.modal.find('.bv-mobile-modal-book');
            
            if (hasSelectedDates) {
                mobileButton.removeAttr('disabled');
            } else {
                mobileButton.attr('disabled', 'disabled');
            }
        }

        // En la clase MobileBooking, modificar el método syncWithOriginalForm:

syncWithOriginalForm() {
    const maxLength = parseInt(this.originalDateInputs.first().data('max-length')) || 1;
    const isSingleMode = maxLength === 1;

    this.originalDateInputs.each((index, input) => {
        // Si es modo single, usar la primera fecha para ambos inputs
        if (isSingleMode && this.selectedDates[0]) {
            $(input).val(this.selectedDates[0].iso).trigger('change');
        } 
        // Si es modo range, usar la fecha correspondiente
        else if (this.selectedDates[index]) {
            $(input).val(this.selectedDates[index].iso).trigger('change');
        }
    });

    // Forzar recálculo en el BookingForm principal
    if (this.bookingFormInstance && typeof this.bookingFormInstance.calculateTotals === 'function') {
        this.bookingFormInstance.calculateTotals();
    } else {
        this.findBookingFormInstance();
        if (this.bookingFormInstance) {
            this.bookingFormInstance.calculateTotals();
        }
    }

    // Actualizar precios después del cálculo
    this.updatePrices();
}



        updateDatesDisplay() {
            const datesText = this.selectedDates.length ? 
                this.selectedDates.map(d => d.display).join(' - ') : 
                'Select dates';

            this.footer.find('.bv-mobile-dates').text(datesText);
            this.modal.find('.bv-mobile-selected-dates').text(datesText);
            
            const $changeButton = this.modal.find('.bv-mobile-change-dates');
            $changeButton.text(this.selectedDates.length ? 'Change' : 'Select dates');
        }

        updateModalPrices() {
            const totalsHtml = this.totalContainer.html();
            if (totalsHtml) {
                // Modificar el HTML para agregar botones de eliminar a los extras
                const $totals = $('<div>').html(totalsHtml);
                
                $totals.find('.bv-total-item').each((_, item) => {
                    const $item = $(item);
                    // Solo agregar botón si es un extra (no al precio base ni al total)
                    if (!$item.hasClass('bv-total-final') && 
                        !$item.find('span:first').text().includes('Base price')) {
                        
                        // Modificar la estructura del nombre del extra para incluir el botón
                        const $firstSpan = $item.find('span:first');
					const fullText = $firstSpan.text();
					const extraName = fullText.split(' × ')[0];  // Obtener solo el nombre
					$firstSpan.html(`
					   ${fullText}
					   <button class="bv-remove-extra" data-extra-name="${extraName}" title="Remove">&times;</button>
					`);
                    }
                });

                this.priceDetails.html($totals.html());

                // Agregar event listeners a los botones de eliminar
this.priceDetails.find('.bv-remove-extra').on('click', (e) => {
   e.stopPropagation();
   e.preventDefault();
   
   const $button = $(e.target);
   const $item = $button.closest('.bv-total-item');
   const extraName = $button.data('extra-name');
   
   console.log('Removing extra:', extraName); // Debug
   
   this.extrasContainer.find('input[type="checkbox"], .bv-extra-quantity').each((_, input) => {
       const $input = $(input);
       const inputName = $input.data('name');
       console.log('Comparing with input:', {
           extraName: String(extraName),
           inputName: String(inputName),
           inputType: $input.attr('type') || 'quantity'
       });
       
       if (String(inputName) === String(extraName)) {
           console.log('Match found!');
           if ($input.is('input[type="checkbox"]')) {
               $input.prop('checked', false).trigger('change');
           } else {
               $input.val(0).trigger('change');
           }
       }
   });

   // Eliminar visualmente del popup
   $item.remove();
   
   // Forzar recálculo de totales
   if (this.bookingFormInstance && typeof this.bookingFormInstance.calculateTotals === 'function') {
       this.bookingFormInstance.calculateTotals();
   }
   
   // Actualizar los precios en el popup
   this.updateModalPrices();
});
            }
        }

        updatePrices() {
            const totalElement = this.totalContainer.find('.bv-total-final span:last-child');
            if (totalElement.length) {
                this.currentTotal = totalElement.text();
                this.footer.find('.bv-mobile-price').text(this.currentTotal);
                this.updateModalPrices();
            }
        }

        validateDates() {
            return this.selectedDates.length > 0;
        }

        openModal() {
            this.updateModalPrices();
            this.modal.addClass('active');
        }

        closeModal() {
            this.modal.removeClass('active');
        }
    }

    // Inicializar cuando el documento esté listo
    $(document).ready(() => {
        new MobileBooking();
    });

})(jQuery);