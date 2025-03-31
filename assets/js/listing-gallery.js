(function($) {
    'use strict';

    class GalleryLightbox {
        constructor() {
            this.images = [];
            this.currentIndex = 0;
            this.lightbox = $('.bv-lightbox');
            this.content = $('.bv-lightbox-content');
            this.counter = $('.bv-lightbox-counter');
            this.initEvents();
        }

        initEvents() {
            // Abrir lightbox
            $('.bv-gallery-item').on('click', (e) => {
                const items = $('.bv-gallery-item');
                this.images = items.map((i, item) => {
                    const mediaEl = $(item).find('img, video').first();
                    return {
                        type: mediaEl.prop('tagName').toLowerCase(),
                        url: mediaEl.data('full') || mediaEl.attr('src')
                    };
                }).get();

                this.currentIndex = items.index($(e.currentTarget));
                this.open();
            });

            // Cerrar lightbox
            $('.bv-lightbox-close').on('click', () => this.close());
			
			// Agregar este manejador
			$(document).on('click', '.bv-lightbox', function(e) {
				if (e.target === this) {  // Solo si se hace clic en el fondo
					$(this).removeClass('active');
				}
			});

            // Navegación
            $('.bv-lightbox-prev').on('click', () => this.prev());
            $('.bv-lightbox-next').on('click', () => this.next());

            // Teclas
            $(document).on('keydown', (e) => {
                if (!this.lightbox.hasClass('active')) return;
                
                switch(e.key) {
                    case 'Escape': this.close(); break;
                    case 'ArrowLeft': this.prev(); break;
                    case 'ArrowRight': this.next(); break;
                }
            });
        }

        open() {
            this.lightbox.addClass('active');
            this.updateContent();
        }

        close() {
            this.lightbox.removeClass('active');
        }

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.updateContent();
        }

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.updateContent();
        }

        updateContent() {
            const item = this.images[this.currentIndex];
            let content;

            if (item.type === 'video') {
                content = `<video src="${item.url}" controls autoplay></video>`;
            } else {
                content = `<img src="${item.url}" alt="">`;
            }

            this.content.html(content);
            this.counter.text(`${this.currentIndex + 1} / ${this.images.length}`);
        }
    }

    // Inicializar cuando el documento está listo
    $(document).ready(() => {
        new GalleryLightbox();
    });

})(jQuery);