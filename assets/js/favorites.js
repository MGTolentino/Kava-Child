jQuery(document).ready(function($) {
    $(document).on('click', '.hp-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
       
        var $toggle = $(this);
        var listingId = $toggle.data('listing-id');
        
        if (!listingId) {
            return;
        }
       
        $.ajax({
            url: hp_favorites.ajax_url,
            method: 'POST',
            data: {
                action: 'toggle_custom_favorite',
                listing_id: listingId
            },
            success: function(response) {
                if (response.success) {
                    $toggle.toggleClass('hp-state-active');
                    var $text = $toggle.find('span');
                    var newText = $toggle.hasClass('hp-state-active') ? 'Remove from Favorites' : 'Add to Favorites';
                    $text.text(newText);
                }
            }
        });
    });
});