<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Step;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Step extends Widget_Base
{
    public function get_name(): string { return 'eek-step'; }
    public function get_title(): string { return esc_html__('EEK Step / Timeline Item', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-step']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('number', ['label' => esc_html__('Number / Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '01']);
        $this->add_control('icon', ['label' => esc_html__('Icon', 'elementor-extension-kit'), 'type' => Controls_Manager::ICONS]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Define the goal', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('text', ['label' => esc_html__('Text', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('Describe this step clearly and keep collection behavior outside this element.', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('meta', ['label' => esc_html__('Meta', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '', 'dynamic' => ['active' => true]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $number = trim((string) ($settings['number'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $text = trim((string) ($settings['text'] ?? ''));
        $meta = trim((string) ($settings['meta'] ?? ''));
        $icon = $settings['icon'] ?? [];
        if ($number === '' && $title === '' && $text === '' && $meta === '' && empty($icon['value'])) { return; }
        ?>
        <article class="eek-step">
            <div class="eek-step__marker" aria-hidden="true">
                <?php if (!empty($icon['value'])) { Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); } elseif ($number !== '') { echo esc_html($number); } ?>
            </div>
            <div class="eek-step__body">
                <?php if ($meta !== '') : ?><div class="eek-step__meta"><?php echo esc_html($meta); ?></div><?php endif; ?>
                <?php if ($title !== '') : ?><h3 class="eek-step__title"><?php echo esc_html($title); ?></h3><?php endif; ?>
                <?php if ($text !== '') : ?><div class="eek-step__text"><?php echo nl2br(esc_html($text)); ?></div><?php endif; ?>
            </div>
        </article>
        <?php
    }
}
