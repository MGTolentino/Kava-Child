(function($) {
    'use strict';
    $(document).ready(function() {
        const nav = $('.bv-sections-nav');
        const images = $('.bv-listing-gallery');
        const links = $('.bv-section-link');
        const bookingActions = $('.bv-nav-booking-actions');
        
        // Clonar el botón y total cuando se inicializa el nav
        const originalButton = $('.bv-booking-button');
        const originalTotal = $('.bv-total-final');
        const navButton = originalButton.clone(true, true);
        const navTotal = originalTotal.clone();
		
		// Modificar el event handler del botón nav
			navButton.on('click', function(e) {
				e.preventDefault();
				// Encontrar y disparar click en el botón original
				$('.bv-booking-form .bv-booking-button').click();
			});
        
        $('.bv-nav-book-button').append(navButton);
        $('.bv-nav-total').append(navTotal);

        function updateNavBooking(currentSection) {
            // Mostrar en todas las secciones excepto 'resumen'
            if(currentSection !== 'resumen') {
                bookingActions.show();
            } else {
                bookingActions.hide();
            }
        }
		
        // Función para actualizar el total en el nav
        function updateNavTotal() {
            const totalElement = $('.bv-booking-form .bv-totals-container .bv-total-final span:last-child');
            if(totalElement.length) {
                const totalText = totalElement.text();
                $('.bv-nav-total').text(totalText);
            }
        }

        // Llamar inicialmente
        updateNavTotal();

        // Observer para actualizaciones
        const observer = new MutationObserver(updateNavTotal);
        if($('.bv-booking-form .bv-totals-container').length) {
            observer.observe($('.bv-booking-form .bv-totals-container')[0], {
                childList: true,
                subtree: true,
                characterData: true
            });
        }
        
        // Función para actualizar link activo y línea indicadora
        function updateActiveSection() {
    const scrollPosition = $(window).scrollTop();
    
    // Iterar sobre cada sección
    links.each(function() {
        const currentLink = $(this);
        const targetId = currentLink.attr('href').substring(1); // remove #
        let $target;

        // Detectar la sección correcta basado en el ID
        switch(targetId) {
            case 'resumen':
                $target = $('.bv-resume-section');
                break;
            case 'extras':
                $target = $('.hp-price-extras-container');
                break;
            case 'ubicacion':
                $target = $('.bv-location-section');
                break;
            case 'resenas':
                $target = $('.bv-reviews-section');
                break;
            case 'detalles':
                $target = $('.bv-details-section');
                break;
        }
        
        if($target && $target.length) {
            const sectionTop = $target.offset().top - 100;
            const sectionBottom = sectionTop + $target.outerHeight();
            
            if(scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                links.removeClass('active');
                currentLink.addClass('active');
                updateNavBooking(targetId);
            }
        }
    });
}
        
        // Función para mostrar/ocultar nav
        function handleNav() {
    if (images.length) {
        const navTriggerPoint = images.offset().top + images.outerHeight();
        const scrollTop = $(window).scrollTop();
        
        if (scrollTop >= navTriggerPoint) {
            nav.addClass('nav-visible');
        } else {
            nav.removeClass('nav-visible');
        }
    }
}

        // Click handler para scroll suave
        // Click handler para scroll suave
links.on('click', function(e) {
    e.preventDefault();
    const targetId = $(this).attr('href').substring(1);
    let $target;
    
    // Usar la misma lógica de detección
    switch(targetId) {
        case 'resumen':
            $target = $('.bv-resume-section');
            break;
        case 'extras':
            $target = $('.hp-price-extras-container');
            break;
        case 'ubicacion':
            $target = $('.bv-location-section');
            break;
        case 'resenas':
            $target = $('.bv-reviews-section');
            break;
        case 'detalles':
            $target = $('.bv-details-section');
            break;
    }
    
    if($target && $target.length) {
        const offset = $target.offset().top - 80;
        $('html, body').animate({
            scrollTop: offset
        }, 300);
        
        links.removeClass('active');
        $(this).addClass('active');
    }
});
        
        // Event listeners
        handleNav();
        updateActiveSection();
        $(window).on('scroll', function() {
            handleNav();
            updateActiveSection();
        });
    });
})(jQuery);