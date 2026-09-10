<?php

defined('ABSPATH') || exit;
?>
<article class="eek-card eek-card--overlay">
    <div class="eek-card__overlay">
        <?php if (! empty($settings['title'])) : ?>
            <h3 class="eek-card__title"><?php echo esc_html((string) $settings['title']); ?></h3>
        <?php endif; ?>

        <?php if (! empty($settings['description'])) : ?>
            <div class="eek-card__description"><?php echo wp_kses_post((string) $settings['description']); ?></div>
        <?php endif; ?>
    </div>
</article>
