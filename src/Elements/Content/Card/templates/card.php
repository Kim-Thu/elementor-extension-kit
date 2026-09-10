<?php

/**
 * Card presentation template.
 *
 * @var array{title:string,description:string,has_title:bool,has_description:bool} $view
 */

defined('ABSPATH') || exit;
?>
<article class="eek-card">
    <?php if ($view['has_title']) : ?>
        <h3 class="eek-card__title"><?php echo esc_html($view['title']); ?></h3>
    <?php endif; ?>

    <?php if ($view['has_description']) : ?>
        <div class="eek-card__description"><?php echo wp_kses_post($view['description']); ?></div>
    <?php endif; ?>
</article>
