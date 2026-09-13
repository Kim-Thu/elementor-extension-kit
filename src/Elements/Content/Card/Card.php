<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Card;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Elementor\ElementDefaultResolver;
use ElementorExtensionKit\Elementor\ElementInstanceInheritance;

final class Card extends Widget_Base
{
    public function get_name(): string { return 'eek-card'; }
    public function get_title(): string { return esc_html__('EEK Card', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-card']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('layout_source', [
            'label' => esc_html__('Layout source', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'inherit',
            'options' => ['inherit' => esc_html__('Inherit global', 'elementor-extension-kit'), 'override' => esc_html__('Override locally', 'elementor-extension-kit')],
        ]);
        $this->add_control('layout_override', [
            'label' => esc_html__('Layout', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => ['default' => esc_html__('Default', 'elementor-extension-kit'), 'overlay' => esc_html__('Overlay', 'elementor-extension-kit')],
            'condition' => ['layout_source' => 'override'],
        ]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Card title', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('description', ['label' => esc_html__('Description', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('Card description.', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $local = ElementInstanceInheritance::local($settings, 'layout');
        $layout = (string) ElementDefaultResolver::resolve('card', 'layout', $local['value'], $local['has_local'], 'default');
        if (! in_array($layout, ['default', 'overlay'], true)) { $layout = 'default'; }
        $template = __DIR__ . '/templates/card' . ucfirst($layout) . '.php';
        if (is_readable($template)) { require $template; }
    }
}
