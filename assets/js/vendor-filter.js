jQuery(document).ready(function($) {
    // Función principal para cargar los resultados
    function loadResults(paged = 1) {
    
	const $form = $('#bv-vendor-filter-form');
    const $results = $('.hp-listings.hp-block.hp-grid');
    
    // Obtener el vendor_id del atributo data del formulario
    const vendorId = $form.data('vendor-id');
    
    let formData = new FormData($form[0]);
    formData.append('action', 'bv_vendor_filter');
    formData.append('nonce', bvVendorFilter.nonce);
    formData.append('vendor_id', vendorId);
    formData.append('paged', paged);
		
		// Dentro de la función loadResults, antes del $.ajax
console.log('Form data debug:', {
    form_exists: $form.length > 0,
    vendor_id: vendorId,
    form_data_array: Array.from(formData.entries())
});

    $.ajax({
        url: bvVendorFilter.ajax_url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            console.log('Enviando petición AJAX...');
            $results.html('<p class="hp-loading">Loading...</p>');
        },
        success: function(response) {
            console.log('Respuesta recibida:', response);
            if (response.success && response.data) {
                console.log('Longitud del HTML:', response.data.html.length);
                $results.html(response.data.html);
                updatePagination(response.data.pagination);
            } else {
                console.error('Respuesta no válida:', response);
                $results.html('<p class="hp-error">Error loading results.</p>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error AJAX:', textStatus, errorThrown);
            $results.html('<p class="hp-error">Error connecting to server.</p>');
        }
    });
}

    // Función para actualizar la paginación
    function updatePagination(pagination) {
        if (!pagination || pagination.totalPages <= 1) {
            $('.hp-filter-pagination').empty();
            return;
        }

        let html = '<div class="hp-pagination">';
        
        // Botón Anterior
        if (pagination.currentPage > 1) {
            html += `<a href="#" class="hp-page-link" data-page="${pagination.currentPage - 1}">Previous</a>`;
        }

        // Páginas
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (
                i === 1 || 
                i === pagination.totalPages || 
                (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)
            ) {
                html += `<a href="#" class="hp-page-link ${i === pagination.currentPage ? 'hp-current' : ''}" data-page="${i}">${i}</a>`;
            } else if (
                i === pagination.currentPage - 3 || 
                i === pagination.currentPage + 3
            ) {
                html += '<span class="hp-pagination-dots">...</span>';
            }
        }

        // Botón Siguiente
        if (pagination.currentPage < pagination.totalPages) {
            html += `<a href="#" class="hp-page-link" data-page="${pagination.currentPage + 1}">Next</a>`;
        }

        html += '</div>';
        $('.hp-filter-pagination').html(html);
    }

    // Event Listeners
    
    // Manejar envío del formulario
    $('#bv-vendor-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadResults(1);
    });

    // Manejar clics en la paginación
    $(document).on('click', '.hp-page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadResults(page);
        
        // Scroll suave hacia los resultados
        $('html, body').animate({
            scrollTop: $('.hp-listings.hp-block.hp-grid').offset().top - 50
        }, 500);
    });
	
	// Agregar el manejador para el botón reset
$('#bv-filter-reset').on('click', function(e) {
    e.preventDefault();
    
    // Resetear los campos del formulario
    const $form = $('#bv-vendor-filter-form');
    const $dateInput = $('#bv-event-date');
    const $searchInput = $('#bv-service-search');
    
    // Limpiar los campos
    $dateInput.val('');
    $searchInput.val('');
    
    // Cargar resultados sin filtros
    loadResults(1);
});

    // Cargar resultados iniciales
    //loadResults();  // Comentado para evitar carga inicial innecesaria
});