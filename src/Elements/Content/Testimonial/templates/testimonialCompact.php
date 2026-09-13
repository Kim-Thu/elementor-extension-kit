<?php
/** @var string $quote */
/** @var string $author */
/** @var string $role */
/** @var string $avatarUrl */
/** @var int $rating */
?>
<figure class="eek-testimonial eek-testimonial--compact">
    <?php if ($quote !== '') : ?><blockquote class="eek-testimonial__quote"><p><?php echo nl2br(esc_html($quote)); ?></p></blockquote><?php endif; ?>
    <div class="eek-testimonial__compact-meta">
        <?php if ($author !== '' || $role !== '' || $avatarUrl !== '') : ?>
            <figcaption class="eek-testimonial__author">
                <?php if ($avatarUrl !== '') : ?><img class="eek-testimonial__avatar" src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($author); ?>" loading="lazy"><?php endif; ?>
                <span class="eek-testimonial__identity">
                    <?php if ($author !== '') : ?><strong class="eek-testimonial__name"><?php echo esc_html($author); ?></strong><?php endif; ?>
                    <?php if ($role !== '') : ?><span class="eek-testimonial__role"><?php echo esc_html($role); ?></span><?php endif; ?>
                </span>
            </figcaption>
        <?php endif; ?>
        <?php if ($rating > 0) : ?>
            <div class="eek-testimonial__rating" role="img" aria-label="<?php echo esc_attr(sprintf(esc_html__('Rated %1$d out of %2$d', 'elementor-extension-kit'), $rating, 5)); ?>">
                <span aria-hidden="true"><?php echo esc_html(str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)); ?></span>
            </div>
        <?php endif; ?>
    </div>
</figure>
