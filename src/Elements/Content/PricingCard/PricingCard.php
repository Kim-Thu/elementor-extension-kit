<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\PricingCard;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class PricingCard extends Widget_Base
{
    public function get_name(): string
    {
        return 'eek-pricing-card';
    }

    public function get_title(): string
    {
        return esc_html__('EEK Pricing Card', 'elementor-extension-kit');
    }

    public function get_icon(): string
    {
        return 'eek-brand-mark';
    }

    public function get_categories(): array
    {
        return [Plugin::ELEMENT_CATEGORY];
    }

    public function get_style_depends(): array
    {
        return ['eek-pricing-card'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'elementor-extension-kit'),
        ]);

        $this->add_control('badge', [
            'label' => esc_html__('Badge', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
        ]);

        $this->add_control('title', [
            'label' => esc_html__('Title', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Professional', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('description', [
            'label' => esc_html__('Description', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('currency', [
            'label' => esc_html__('Currency', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '$',
        ]);

        $this->add_control('price', [
            'label' => esc_html__('Price', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '49',
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('period', [
            'label' => esc_html__('Period', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('/ month', 'elementor-extension-kit'),
        ]);

        $repeater = new Repeater();
        $repeater->add_control('text', [
            'label' => esc_html__('Feature', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Feature item', 'elementor-extension-kit'),
        ]);

        $this->add_control('features', [
            'label' => esc_html__('Features', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['text' => esc_html__('Core feature', 'elementor-extension-kit')],
                ['text' => esc_html__('Priority support', 'elementor-extension-kit')],
            ],
            'title_field' => '{{{ text }}}',
        ]);

        $this->add_control('cta_text', [
            'label' => esc_html__('CTA text', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Get started', 'elementor-extension-kit'),
        ]);

        $this->add_control('cta_link', [
            'label' => esc_html__('CTA link', 'elementor-extension-kit'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com',
            'condition' => ['cta_text!' => ''],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $badge = trim((string) ($settings['badge'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $description = trim((string) ($settings['description'] ?? ''));
        $currency = trim((string) ($settings['currency'] ?? ''));
        $price = trim((string) ($settings['price'] ?? ''));
        $period = trim((string) ($settings['period'] ?? ''));
        $features = is_array($settings['features'] ?? null) ? $settings['features'] : [];
        $ctaText = trim((string) ($settings['cta_text'] ?? ''));
        $ctaLink = is_array($settings['cta_link'] ?? null) ? $settings['cta_link'] : [];

        if ($ctaText !== '' && ! empty($ctaLink['url'])) {
            $this->add_link_attributes('cta', $ctaLink);
            $this->add_render_attribute('cta', 'class', 'eek-pricing-card__cta');
        }
        ?>
        <article class="eek-pricing-card">
            <?php if ($badge !== '') : ?>
                <span class="eek-pricing-card__badge"><?php echo esc_html($badge); ?></span>
            <?php endif; ?>

            <?php if ($title !== '') : ?>
                <h3 class="eek-pricing-card__title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>

            <?php if ($description !== '') : ?>
                <p class="eek-pricing-card__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if ($price !== '' || $currency !== '' || $period !== '') : ?>
                <p class="eek-pricing-card__price" aria-label="<?php echo esc_attr(trim($currency . $price . ' ' . $period)); ?>">
                    <?php if ($currency !== '') : ?><span class="eek-pricing-card__currency"><?php echo esc_html($currency); ?></span><?php endif; ?>
                    <?php if ($price !== '') : ?><strong class="eek-pricing-card__amount"><?php echo esc_html($price); ?></strong><?php endif; ?>
                    <?php if ($period !== '') : ?><span class="eek-pricing-card__period"><?php echo esc_html($period); ?></span><?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if ($features !== []) : ?>
                <ul class="eek-pricing-card__features">
                    <?php foreach ($features as $feature) : ?>
                        <?php $featureText = trim((string) ($feature['text'] ?? '')); ?>
                        <?php if ($featureText !== '') : ?>
                            <li><?php echo esc_html($featureText); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($ctaText !== '' && ! empty($ctaLink['url'])) : ?>
                <a <?php $this->print_render_attribute_string('cta'); ?>><?php echo esc_html($ctaText); ?></a>
            <?php endif; ?>
        </article>
        <?php
    }
}
