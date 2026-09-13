<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Testimonial;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Elementor\ElementInstanceInheritance;
use ElementorExtensionKit\Elementor\ElementTemplateRegistry;
use ElementorExtensionKit\Elementor\ElementTemplateResolver;

final class Testimonial extends Widget_Base
{
    public function get_name(): string { return 'eek-testimonial'; }
    public function get_title(): string { return esc_html__('EEK Testimonial', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-testimonial']; }

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
        $this->add_control('quote', [
            'label' => esc_html__('Quote', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__('A clear testimonial helps visitors understand the value of your work.', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('author', [
            'label' => esc_html__('Author', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Customer name', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('role', [
            'label' => esc_html__('Role / Company', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('avatar', ['label' => esc_html__('Avatar', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('rating', [
            'label' => esc_html__('Rating', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => '5',
            'options' => ['0' => esc_html__('None', 'elementor-extension-kit'), '1' => '1 / 5', '2' => '2 / 5', '3' => '3 / 5', '4' => '4 / 5', '5' => '5 / 5'],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $quote = trim((string) ($settings['quote'] ?? ''));
        $author = trim((string) ($settings['author'] ?? ''));
        $role = trim((string) ($settings['role'] ?? ''));
        $avatarUrl = (string) ($settings['avatar']['url'] ?? '');
        $rating = max(0, min(5, (int) ($settings['rating'] ?? 0)));

        if ($quote === '' && $author === '' && $role === '' && $avatarUrl === '' && $rating === 0) {
            return;
        }

        $local = ElementInstanceInheritance::local($settings, 'template');
        $template = ElementTemplateResolver::resolve('testimonial', $local['value'], $local['has_local']);
        $path = is_array($template) && is_string($template['path'] ?? null)
            ? $template['path']
            : __DIR__ . '/templates/testimonialDefault.php';

        if (is_readable($path)) {
            require $path;
        }
    }

    private static function templateOptions(): array
    {
        $options = ['default' => esc_html__('Default / Native', 'elementor-extension-kit')];
        foreach (ElementTemplateRegistry::forElement('testimonial') as $id => $template) {
            $options[$id] = (string) ($template['name'] ?? $id);
        }
        return $options;
    }
}
