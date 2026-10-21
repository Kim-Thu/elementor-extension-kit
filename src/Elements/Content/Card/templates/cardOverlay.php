<?php

defined('ABSPATH') || exit;

$url = esc_url((string) ($card['url'] ?? ''));
$rel = implode(' ', array_filter([
    ! empty($card['external']) ? 'noopener noreferrer' : '',
    ! empty($card['nofollow']) ? 'nofollow' : '',
]));
?>
<article class="eek-card eek-card--overlay">
    <?php if (! empty($card['image'])) : ?>
        <div class="eek-card__media"><?php echo wp_kses_post((string) $card['image']); ?></div>
    <?php endif; ?>
    <div class="eek-card__overlay">
        <?php if (! empty($card['date'])) : ?>
            <div class="eek-card__date"><?php echo esc_html((string) $card['date']); ?></div>
        <?php endif; ?>
        <?php if (! empty($card['title'])) : ?>
            <h3 class="eek-card__title">
                <?php if ($url !== '') : ?><a class="eek-card__link" href="<?php echo esc_url($url); ?>"<?php echo ! empty($card['external']) ? ' target="_blank"' : ''; ?><?php echo $rel !== '' ? ' rel="' . esc_attr($rel) . '"' : ''; ?>><?php endif; ?>
                <?php echo esc_html((string) $card['title']); ?>
                <?php if ($url !== '') : ?></a><?php endif; ?>
            </h3>
        <?php endif; ?>
        <?php if (! empty($card['description'])) : ?>
            <div class="eek-card__description"><?php echo wp_kses_post((string) $card['description']); ?></div>
        <?php endif; ?>
    </div>
</article>
