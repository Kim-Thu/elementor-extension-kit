<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Badge;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Badge extends Widget_Base
{
    public function get_name(): string { return 'eek-badge'; }
    public function get_title(): string { return esc_html__('EEK Badge', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-badge']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('text', ['label' => esc_html__('Text', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('New', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('tone', ['label' => esc_html__('Tone', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'neutral', 'options' => ['neutral' => esc_html__('Neutral', 'elementor-extension-kit'), 'success' => esc_html__('Success', 'elementor-extension-kit'), 'warning' => esc_html__('Warning', 'elementor-extension-kit'), 'danger' => esc_html__('Danger', 'elementor-extension-kit')]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $text = trim((string) ($settings['text'] ?? ''));
        $allowed = ['neutral', 'success', 'warning', 'danger'];
        $tone = in_array(($settings['tone'] ?? ''), $allowed, true) ? (string) $settings['tone'] : 'neutral';
        if ($text === '') { return; }
        ?><span class="eek-badge eek-badge--<?php echo esc_attr($tone); ?>"><?php echo esc_html($text); ?></span><?php
    }
}
