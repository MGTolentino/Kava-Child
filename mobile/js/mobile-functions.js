jQuery(document).ready(function($) {
    // Clase para manejar el carrusel
    class MobileGallery {
        constructor() {
            // Elementos del DOM - específicamente de la galería principal
            this.gallery = $('.bv-gallery-grid');
            this.items = $('.bv-gallery-grid .bv-gallery-item'); // Más específico
            this.totalItems = this.items.length;
            this.currentIndex = 0;

            // Crear indicador si no existe
            if (!$('.bv-gallery-indicator').length) {
                this.gallery.append(`
                    <div class="bv-gallery-indicator">
                        1/${this.totalItems}
                    </div>
                `);
            }

            // Variables para el swipe
            this.touchStartX = 0;
            this.touchEndX = 0;

            this.init();
        }

        init() {
            // Mostrar primera imagen
            $(this.items[0]).addClass('active');
            
            // Eventos touch
            this.gallery
                .on('touchstart', (e) => this.handleTouchStart(e))
                .on('touchmove', (e) => this.handleTouchMove(e))
                .on('touchend', (e) => this.handleTouchEnd(e));
        }

        // Manejo de eventos táctiles
        handleTouchStart(e) {
            this.touchStartX = e.originalEvent.touches[0].clientX;
        }

        handleTouchMove(e) {
            this.touchEndX = e.originalEvent.touches[0].clientX;
        }

        handleTouchEnd() {
            const swipeThreshold = 50; // píxeles mínimos para considerar swipe
            const diff = this.touchStartX - this.touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    this.nextSlide(); // Swipe izquierda
                } else {
                    this.prevSlide(); // Swipe derecha
                }
            }
        }

        // Navegación
        nextSlide() {
            if (this.currentIndex < this.totalItems - 1) {
                this.goToSlide(this.currentIndex + 1);
            }
        }

        prevSlide() {
            if (this.currentIndex > 0) {
                this.goToSlide(this.currentIndex - 1);
            }
        }

        goToSlide(index) {
            // Remover clase active actual
            $(this.items[this.currentIndex]).removeClass('active');
            
            // Actualizar índice y activar nueva imagen
            this.currentIndex = index;
            $(this.items[this.currentIndex]).addClass('active');

            // Actualizar indicador
            $('.bv-gallery-indicator').text(`${this.currentIndex + 1}/${this.totalItems}`);
        }
    }

    // Inicializar solo en móvil y si existe la galería
    if (window.innerWidth <= 768 && $('.bv-gallery-grid').length) {
        new MobileGallery();
    }
	
	function initializeReviewsModal() {
   // Verificar cada reseña y agregar botón solo si no existe
   $('.review-content').each(function() {
       const content = $(this);
       
       // Verificar si el contenido está truncado y no tiene ya un botón
       if (this.scrollHeight > this.clientHeight && !content.next('.review-show-more').length) {
           content.after(`
               <button class="review-show-more">Show more</button>
           `);
       }
   });

   // Agregar modal al body si no existe
   if (!$('.review-modal').length) {
       $('body').append(`
           <div class="review-modal">
               <div class="review-modal-content">
                   <button class="review-modal-close">&times;</button>
                   <div class="review-modal-text"></div>
               </div>
           </div>
       `);
   }

   // Remover eventos previos para evitar duplicados
   $(document).off('click', '.review-show-more');

   // Manejar click en "Show more"
   $(document).on('click', '.review-show-more', function() {
       const reviewItem = $(this).closest('.review-item');
       const reviewContent = reviewItem.find('.review-content');
       const fullText = reviewContent.text();
       const authorName = reviewItem.find('.review-author').text();
       
       $('.review-modal-text').html(`
           <h3 style="margin-bottom: 16px;">${authorName}'s Review</h3>
           <p>${fullText}</p>
       `);
       $('.review-modal').fadeIn(200);
   });

   // Remover eventos previos del modal
   $('.review-modal-close, .review-modal').off('click');

   // Cerrar modal
   $('.review-modal-close, .review-modal').on('click', function(e) {
       if (e.target === this) {
           $('.review-modal').fadeOut(200);
       }
   });
}
	
	// Carrusel de reseñas
function initializeReviewsCarousel() {
    const reviewsGrid = $('.reviews-grid');
    const reviewItems = $('.review-item');

    // Solo inicializar si estamos en móvil y hay reseñas
    if (window.innerWidth <= 768 && reviewItems.length) {
        // Ocultar reseñas después de la sexta
        reviewItems.each(function(index) {
            if (index >= 6) {
                $(this).hide();
            }
        });

        // Añadir botón "Show all reviews" si hay más de 6 reseñas
        if (reviewItems.length > 6 && !$('.show-more-reviews').length) {
            reviewsGrid.after(`
                <button class="show-more-reviews">
                    Show all ${reviewItems.length} reviews
                </button>
            `);

            // Manejar click en "Show all reviews"
            $('.show-more-reviews').on('click', function() {
                reviewItems.show();
                $(this).hide();
                // Resetear el scroll del contenedor
                reviewsGrid.scrollLeft(0);
                // Cambiar el layout a vertical para todas las reseñas
                reviewsGrid.css({
                    'display': 'grid',
                    'grid-template-columns': '1fr',
                    'gap': '24px',
                    'overflow': 'visible'
                });
                // Ajustar estilo de los items
                reviewItems.css({
                    'flex': 'none',
                    'width': '100%'
                });
            });
        }

        // Mejorar el scroll con snap points
        let isScrolling;
        reviewsGrid.on('scroll', function() {
            window.clearTimeout(isScrolling);
            isScrolling = setTimeout(function() {
                // Encuentra el punto de snap más cercano
                const scrollLeft = reviewsGrid.scrollLeft();
                const itemWidth = reviewItems.first().outerWidth(true);
                const targetScroll = Math.round(scrollLeft / itemWidth) * itemWidth;
                
                reviewsGrid.stop().animate({
                    scrollLeft: targetScroll
                }, 150);
            }, 66);
        });
    }
	
	    initializeReviewsModal();

}

// Llamar a la función cuando el documento esté listo
$(document).ready(function() {
    initializeReviewsCarousel();

    // Reinicializar en cambio de tamaño de ventana
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initializeReviewsCarousel();
        }, 250);
    });
});
	
});