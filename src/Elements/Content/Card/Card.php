<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Card;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Elementor\ElementInstanceInheritance;
use ElementorExtensionKit\Elementor\ElementTemplateRegistry;
use ElementorExtensionKit\Elementor\ElementTemplateResolver;

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
        $this->add_control('template_source', [
            'label' => esc_html__('Template source', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => ElementInstanceInheritance::INHERIT,
            'options' => [
                ElementInstanceInheritance::INHERIT => esc_html__('Inherit global', 'elementor-extension-kit'),
                ElementInstanceInheritance::OVERRIDE => esc_html__('Override locally', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('template_override', [
            'label' => esc_html__('Template', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => self::templateOptions(),
            'condition' => ['template_source' => ElementInstanceInheritance::OVERRIDE],
        ]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Card title', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('description', ['label' => esc_html__('Description', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('Card description.', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $local = ElementInstanceInheritance::local($settings, 'template');
        $template = ElementTemplateResolver::resolve('card', $local['value'], $local['has_local']);
        $path = is_array($template) && is_string($template['path'] ?? null)
            ? $template['path']
            : __DIR__ . '/templates/cardDefault.php';

        if (is_readable($path)) {
            require $path;
        }
    }

    private static function templateOptions(): array
    {
        $options = ['default' => esc_html__('Default / Native', 'elementor-extension-kit')];
        foreach (ElementTemplateRegistry::forElement('card') as $id => $template) {
            $options[$id] = (string) ($template['name'] ?? $id);
        }
        return $options;
    }
}
