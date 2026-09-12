<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Stat;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Stat extends Widget_Base
{
    public function get_name(): string { return 'eek-stat'; }
    public function get_title(): string { return esc_html__('EEK Stat', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-stat']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('icon', ['label' => esc_html__('Icon', 'elementor-extension-kit'), 'type' => Controls_Manager::ICONS]);
        $this->add_control('prefix', ['label' => esc_html__('Prefix', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('value', ['label' => esc_html__('Value', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '120', 'dynamic' => ['active' => true]]);
        $this->add_control('suffix', ['label' => esc_html__('Suffix', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '+']);
        $this->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Projects delivered', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $value = trim((string) ($settings['value'] ?? ''));
        $label = trim((string) ($settings['label'] ?? ''));
        $prefix = trim((string) ($settings['prefix'] ?? ''));
        $suffix = trim((string) ($settings['suffix'] ?? ''));
        $icon = $settings['icon'] ?? [];
        if ($value === '' && $label === '' && empty($icon['value'])) { return; }
        ?>
        <div class="eek-stat">
            <?php if (!empty($icon['value'])) : ?><div class="eek-stat__icon" aria-hidden="true"><?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?></div><?php endif; ?>
            <?php if ($value !== '' || $prefix !== '' || $suffix !== '') : ?><div class="eek-stat__value"><?php echo esc_html($prefix . $value . $suffix); ?></div><?php endif; ?>
            <?php if ($label !== '') : ?><div class="eek-stat__label"><?php echo esc_html($label); ?></div><?php endif; ?>
        </div>
        <?php
    }
}
