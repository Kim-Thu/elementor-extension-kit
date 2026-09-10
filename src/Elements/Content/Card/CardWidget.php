<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Card;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

final class CardWidget extends Widget_Base
{
    public function get_name(): string
    {
        return 'eek-card';
    }

    public function get_title(): string
    {
        return esc_html__('EEK Card', 'elementor-extension-kit');
    }

    public function get_icon(): string
    {
        return 'eicon-info-box';
    }

    public function get_categories(): array
    {
        return ['general'];
    }

    public function get_style_depends(): array
    {
        return ['eek-card'];
    }

    public function get_script_depends(): array
    {
        return ['eek-card'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'elementor-extension-kit'),
        ]);

        $this->add_control('title', [
            'label' => esc_html__('Title', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Card title', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('description', [
            'label' => esc_html__('Description', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__('Card description.', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $view = CardLogic::toViewModel($this->get_settings_for_display());
        $template = __DIR__ . '/templates/card.php';

        if (! is_readable($template)) {
            return;
        }

        require $template;
    }
}
