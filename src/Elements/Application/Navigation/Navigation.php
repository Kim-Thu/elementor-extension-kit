<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\Navigation;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Navigation extends Widget_Base
{
    public function get_name(): string { return 'eek-app-navigation'; }
    public function get_title(): string { return esc_html__('EEK App Navigation', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-app-navigation']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Navigation', 'elementor-extension-kit')]);
        $this->add_control('aria_label', [
            'label' => esc_html__('Accessible label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Application navigation', 'elementor-extension-kit'),
        ]);
        $this->add_control('orientation', [
            'label' => esc_html__('Orientation', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'vertical',
            'options' => [
                'vertical' => esc_html__('Vertical / Sidebar', 'elementor-extension-kit'),
                'horizontal' => esc_html__('Horizontal / Top', 'elementor-extension-kit'),
            ],
        ]);

        $repeater = new Repeater();
        $repeater->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Navigation item', 'elementor-extension-kit')]);
        $repeater->add_control('url', ['label' => esc_html__('URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);
        $repeater->add_control('current', ['label' => esc_html__('Current item', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '']);
        $repeater->add_control('section', ['label' => esc_html__('Section label', 'elementor-extension-kit'), 'description' => esc_html__('Optional text shown before this item.', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('items', [
            'label' => esc_html__('Items', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        if ($items === []) { return; }
        $orientation = ($settings['orientation'] ?? '') === 'horizontal' ? 'horizontal' : 'vertical';
        $ariaLabel = trim((string) ($settings['aria_label'] ?? '')) ?: esc_html__('Application navigation', 'elementor-extension-kit');
        ?>
        <nav class="eek-app-navigation eek-app-navigation--<?php echo esc_attr($orientation); ?>" aria-label="<?php echo esc_attr($ariaLabel); ?>">
            <ul class="eek-app-navigation__list">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $label = trim((string) ($item['label'] ?? ''));
                    $url = is_array($item['url'] ?? null) ? $item['url'] : [];
                    $section = trim((string) ($item['section'] ?? ''));
                    if ($label === '' || empty($url['url'])) { continue; }
                    $key = 'app_nav_' . (int) $index;
                    $this->add_link_attributes($key, $url);
                    $this->add_render_attribute($key, 'class', 'eek-app-navigation__link');
                    if (($item['current'] ?? '') === 'yes') {
                        $this->add_render_attribute($key, 'aria-current', 'page');
                    }
                    ?>
                    <?php if ($section !== '') : ?><li class="eek-app-navigation__section" aria-hidden="true"><?php echo esc_html($section); ?></li><?php endif; ?>
                    <li class="eek-app-navigation__item"><a <?php $this->print_render_attribute_string($key); ?>><?php echo esc_html($label); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }
}
