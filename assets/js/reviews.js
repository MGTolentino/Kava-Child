document.addEventListener('DOMContentLoaded', function() {
    // Selector para el botón de mostrar más
    const showMoreButton = document.querySelector('.show-more-reviews');
    
    if (showMoreButton) {
        showMoreButton.addEventListener('click', function() {
            // Selecciona todas las reseñas ocultas
            const hiddenReviews = document.querySelectorAll('.review-hidden');
            
            // Muestra las reseñas ocultas
            hiddenReviews.forEach(review => {
                review.classList.remove('review-hidden');
            });
            
            // Oculta el botón después de mostrar todas las reseñas
            showMoreButton.style.display = 'none';
        });
    }
});