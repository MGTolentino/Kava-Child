<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

// Obtener el listing actual
$listing = hivepress()->request->get_context('listing');

// Obtener reseñas
$reviews = get_comments([
    'post_id' => $listing->get_id(),
    'type' => 'hp_review',
    'status' => 'approve',
]);
?>

<div class="bv-reviews-section" id="resenas">
        <div class="bv-main-content">
            <h2 class="bv-section-title">Reviews</h2>
            <?php if ($reviews): ?>
                <div class="hp-reviews">
                    <div class="reviews-grid">
                        <?php 
                        $total_reviews = count($reviews);
                        $initial_show = 6; // Número inicial de reseñas a mostrar
                        
                        foreach($reviews as $index => $review): 
                            $rating = get_comment_meta($review->comment_ID, 'hp_rating', true);
                            $hidden_class = $index >= $initial_show ? 'review-hidden' : '';
                        ?>
                            <div class="review-item <?php echo $hidden_class; ?>">
                                <div class="review-header">
                                    <div class="review-avatar">
                                        <?php echo get_avatar($review->comment_author_email, 56); ?>
                                    </div>
                                    <div class="review-meta">
                                        <div class="review-author">
                                            <?php echo esc_html($review->comment_author); ?>
                                        </div>
                                        <div class="review-details">
                                            <div class="hp-review__rating hp-rating-stars" data-component="rating" data-value="<?php echo esc_attr($review->comment_karma); ?>"></div>
                                            <time class="review-date" datetime="<?php echo esc_attr($review->comment_date); ?>">
                                                <?php echo date('F j, Y', strtotime($review->comment_date)); ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <div class="review-text">
                                        <?php echo esc_html($review->comment_content); ?>
                                    </div>
                                    <a href="#review_reply_modal_<?php echo $review->comment_ID; ?>" class="hp-review__action hp-review__action--reply hp-link">
                                        <i class="hp-icon fas fa-share"></i>
                                        <span>Reply</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($total_reviews > $initial_show): ?>
                        <button class="show-more-reviews">
                            Mostrar más evaluaciones
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="bv-no-reviews">No hay reseñas todavía.</p>
            <?php endif; ?>
        </div>
</div>