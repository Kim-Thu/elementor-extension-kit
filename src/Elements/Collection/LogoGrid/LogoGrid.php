<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\LogoGrid;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class LogoGrid extends Widget_Base
{
    public function get_name(): string { return 'eek-logo-grid'; }
    public function get_title(): string { return esc_html__('EEK Logo Grid', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-logo-grid']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Logos', 'elementor-extension-kit')]);

        $repeater = new Repeater();
        $repeater->add_control('logo', ['label' => esc_html__('Logo', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('label', ['label' => esc_html__('Accessible label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Partner', 'elementor-extension-kit')]);
        $repeater->add_control('url', ['label' => esc_html__('URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);

        $this->add_control('items', [
            'label' => esc_html__('Items', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();

        $this->start_controls_section('layout', ['label' => esc_html__('Layout', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('columns', [
            'label' => esc_html__('Columns', 'elementor-extension-kit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 8,
            'default' => 4,
            'tablet_default' => 3,
            'mobile_default' => 2,
            'selectors' => ['{{WRAPPER}} .eek-logo-grid' => '--eek-logo-grid-columns: {{VALUE}};'],
        ]);
        $this->add_responsive_control('gap', [
            'label' => esc_html__('Gap', 'elementor-extension-kit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'rem'],
            'range' => ['px' => ['min' => 0, 'max' => 80], 'rem' => ['min' => 0, 'max' => 5, 'step' => .1]],
            'default' => ['unit' => 'rem', 'size' => 1],
            'selectors' => ['{{WRAPPER}} .eek-logo-grid' => '--eek-logo-grid-gap: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        if ($items === []) { return; }
        ?>
        <ul class="eek-logo-grid" aria-label="<?php echo esc_attr__('Partner logos', 'elementor-extension-kit'); ?>">
            <?php foreach ($items as $index => $item) : ?>
                <?php
                $logoUrl = trim((string) ($item['logo']['url'] ?? ''));
                $label = trim((string) ($item['label'] ?? ''));
                $url = is_array($item['url'] ?? null) ? $item['url'] : [];
                if ($logoUrl === '') { continue; }
                $key = 'logo_' . (int) $index;
                if (! empty($url['url'])) { $this->add_link_attributes($key, $url); }
                ?>
                <li class="eek-logo-grid__item">
                    <?php if (! empty($url['url'])) : ?><a class="eek-logo-grid__link" <?php $this->print_render_attribute_string($key); ?>><?php endif; ?>
                    <img class="eek-logo-grid__image" src="<?php echo esc_url($logoUrl); ?>" alt="<?php echo esc_attr($label); ?>" loading="lazy" decoding="async">
                    <?php if (! empty($url['url'])) : ?></a><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}
