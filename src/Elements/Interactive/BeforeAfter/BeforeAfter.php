<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\BeforeAfter;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class BeforeAfter extends Widget_Base
{
    public function get_name(): string { return 'eek-before-after'; }
    public function get_title(): string { return esc_html__('EEK Before / After', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-before-after']; }
    public function get_script_depends(): array { return ['eek-before-after']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Images', 'elementor-extension-kit')]);
        $this->add_control('before', ['label' => esc_html__('Before image', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('after', ['label' => esc_html__('After image', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('before_label', ['label' => esc_html__('Before label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Before', 'elementor-extension-kit')]);
        $this->add_control('after_label', ['label' => esc_html__('After label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('After', 'elementor-extension-kit')]);
        $this->add_control('position', ['label' => esc_html__('Initial position', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'default' => 50, 'min' => 0, 'max' => 100]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $before = (string) ($settings['before']['url'] ?? '');
        $after = (string) ($settings['after']['url'] ?? '');
        if ($before === '' || $after === '') { return; }
        $position = max(0, min(100, (int) ($settings['position'] ?? 50)));
        $beforeLabel = trim((string) ($settings['before_label'] ?? ''));
        $afterLabel = trim((string) ($settings['after_label'] ?? ''));
        ?>
        <figure class="eek-before-after" data-eek-before-after style="--eek-position:<?php echo esc_attr((string) $position); ?>%">
            <img class="eek-before-after__image" src="<?php echo esc_url($before); ?>" alt="<?php echo esc_attr($beforeLabel); ?>" loading="lazy">
            <div class="eek-before-after__overlay" aria-hidden="true"><img class="eek-before-after__image" src="<?php echo esc_url($after); ?>" alt="" loading="lazy"></div>
            <div class="eek-before-after__divider" aria-hidden="true"></div>
            <input class="eek-before-after__range" type="range" min="0" max="100" value="<?php echo esc_attr((string) $position); ?>" aria-label="<?php echo esc_attr__('Reveal after image', 'elementor-extension-kit'); ?>">
            <figcaption class="eek-before-after__labels"><span><?php echo esc_html($beforeLabel); ?></span><span><?php echo esc_html($afterLabel); ?></span></figcaption>
        </figure>
        <?php
    }
}
