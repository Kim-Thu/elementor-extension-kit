<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\ActiveFilters;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class ActiveFilters extends Widget_Base
{
    public function get_name(): string { return 'eek-active-filters'; }
    public function get_title(): string { return esc_html__('EEK Active Filters', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-chip-group', 'eek-active-filters']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Active filters', 'elementor-extension-kit')]);
        $this->add_control('aria_label', [
            'label' => esc_html__('Group label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Active filters', 'elementor-extension-kit'),
        ]);

        $repeater = new Repeater();
        $repeater->add_control('name', [
            'label' => esc_html__('Query name', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => 'filter',
        ]);
        $repeater->add_control('label', [
            'label' => esc_html__('Label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Filter', 'elementor-extension-kit'),
        ]);

        $this->add_control('filters', [
            'label' => esc_html__('Tracked filters', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ label }}} — {{{ name }}}',
        ]);
        $this->add_control('clear_all_label', [
            'label' => esc_html__('Clear all label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Clear all', 'elementor-extension-kit'),
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $definitions = is_array($settings['filters'] ?? null) ? $settings['filters'] : [];
        $active = [];

        foreach ($definitions as $definition) {
            $name = sanitize_key((string) ($definition['name'] ?? ''));
            if ($name === '' || ! isset($_GET[$name]) || is_array($_GET[$name])) { continue; }

            $value = sanitize_text_field(wp_unslash((string) $_GET[$name]));
            if ($value === '') { continue; }

            $label = trim((string) ($definition['label'] ?? ''));
            $active[] = [
                'name' => $name,
                'label' => $label !== '' ? $label : $name,
                'value' => $value,
            ];
        }

        if ($active === []) { return; }

        $ariaLabel = trim((string) ($settings['aria_label'] ?? '')) ?: esc_html__('Active filters', 'elementor-extension-kit');
        $clearAllLabel = trim((string) ($settings['clear_all_label'] ?? '')) ?: esc_html__('Clear all', 'elementor-extension-kit');
        $names = array_column($active, 'name');
        ?>
        <div class="eek-active-filters">
            <ul class="eek-chip-group eek-active-filters__list" aria-label="<?php echo esc_attr($ariaLabel); ?>">
                <?php foreach ($active as $filter) : ?>
                    <li class="eek-chip-group__item">
                        <a class="eek-chip-group__chip eek-chip-group__chip--link eek-active-filters__chip" href="<?php echo esc_url(remove_query_arg($filter['name'])); ?>" aria-label="<?php echo esc_attr(sprintf(__('Remove filter %1$s: %2$s', 'elementor-extension-kit'), $filter['label'], $filter['value'])); ?>">
                            <span class="eek-active-filters__label"><?php echo esc_html($filter['label']); ?>:</span>
                            <span><?php echo esc_html($filter['value']); ?></span>
                            <span aria-hidden="true">×</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="eek-active-filters__clear" href="<?php echo esc_url(remove_query_arg($names)); ?>"><?php echo esc_html($clearAllLabel); ?></a>
        </div>
        <?php
    }
}
