<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\PricingTable;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class PricingTable extends Widget_Base
{
    public function get_name(): string { return 'eek-pricing-table'; }
    public function get_title(): string { return esc_html__('EEK Pricing Table', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-pricing-table']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Plans', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('name', ['label' => esc_html__('Plan name', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Plan', 'elementor-extension-kit')]);
        $repeater->add_control('badge', ['label' => esc_html__('Badge', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT]);
        $repeater->add_control('currency', ['label' => esc_html__('Currency', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '$']);
        $repeater->add_control('price', ['label' => esc_html__('Price', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '29']);
        $repeater->add_control('period', ['label' => esc_html__('Period', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('/month', 'elementor-extension-kit')]);
        $repeater->add_control('description', ['label' => esc_html__('Description', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('features', ['label' => esc_html__('Features (one per line)', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('cta_label', ['label' => esc_html__('CTA label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Choose plan', 'elementor-extension-kit')]);
        $repeater->add_control('cta_url', ['label' => esc_html__('CTA URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);
        $repeater->add_control('featured', ['label' => esc_html__('Featured', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '']);
        $this->add_control('plans', ['label' => esc_html__('Plans', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [], 'title_field' => '{{{ name }}}']);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $plans = is_array($settings['plans'] ?? null) ? $settings['plans'] : [];
        if ($plans === []) { return; }
        ?>
        <div class="eek-pricing-table" role="list" aria-label="<?php echo esc_attr__('Pricing plans', 'elementor-extension-kit'); ?>">
            <?php foreach ($plans as $index => $plan) : ?>
                <?php
                $name = trim((string) ($plan['name'] ?? ''));
                $badge = trim((string) ($plan['badge'] ?? ''));
                $currency = trim((string) ($plan['currency'] ?? ''));
                $price = trim((string) ($plan['price'] ?? ''));
                $period = trim((string) ($plan['period'] ?? ''));
                $description = trim((string) ($plan['description'] ?? ''));
                $featureLines = preg_split('/\R/u', (string) ($plan['features'] ?? '')) ?: [];
                $featureLines = array_values(array_filter(array_map('trim', $featureLines), static fn(string $line): bool => $line !== ''));
                $ctaLabel = trim((string) ($plan['cta_label'] ?? ''));
                $ctaUrl = is_array($plan['cta_url'] ?? null) ? $plan['cta_url'] : [];
                $featured = ($plan['featured'] ?? '') === 'yes';
                $linkKey = 'pricing_cta_' . (int) $index;
                if (! empty($ctaUrl['url'])) { $this->add_link_attributes($linkKey, $ctaUrl); }
                ?>
                <article class="eek-pricing-table__plan<?php echo $featured ? ' eek-pricing-table__plan--featured' : ''; ?>" role="listitem">
                    <?php if ($badge !== '') : ?><span class="eek-pricing-table__badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
                    <?php if ($name !== '') : ?><h3 class="eek-pricing-table__name"><?php echo esc_html($name); ?></h3><?php endif; ?>
                    <p class="eek-pricing-table__price"><span class="eek-pricing-table__currency"><?php echo esc_html($currency); ?></span><strong><?php echo esc_html($price); ?></strong><?php if ($period !== '') : ?><span class="eek-pricing-table__period"><?php echo esc_html($period); ?></span><?php endif; ?></p>
                    <?php if ($description !== '') : ?><p class="eek-pricing-table__description"><?php echo nl2br(esc_html($description)); ?></p><?php endif; ?>
                    <?php if ($featureLines !== []) : ?><ul class="eek-pricing-table__features"><?php foreach ($featureLines as $feature) : ?><li><?php echo esc_html($feature); ?></li><?php endforeach; ?></ul><?php endif; ?>
                    <?php if ($ctaLabel !== '' && ! empty($ctaUrl['url'])) : ?><a class="eek-pricing-table__cta" <?php $this->print_render_attribute_string($linkKey); ?>><?php echo esc_html($ctaLabel); ?></a><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
