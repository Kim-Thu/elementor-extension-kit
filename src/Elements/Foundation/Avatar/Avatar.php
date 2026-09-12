<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Avatar;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Avatar extends Widget_Base
{
    public function get_name(): string { return 'eek-avatar'; }
    public function get_title(): string { return esc_html__('EEK Avatar', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-avatar']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('image', [
            'label' => esc_html__('Image', 'elementor-extension-kit'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('label', [
            'label' => esc_html__('Accessible label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('User avatar', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('initials', [
            'label' => esc_html__('Initials fallback', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => 'EK',
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('status', [
            'label' => esc_html__('Status', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => esc_html__('None', 'elementor-extension-kit'),
                'online' => esc_html__('Online', 'elementor-extension-kit'),
                'away' => esc_html__('Away', 'elementor-extension-kit'),
                'busy' => esc_html__('Busy', 'elementor-extension-kit'),
                'offline' => esc_html__('Offline', 'elementor-extension-kit'),
            ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('style', [
            'label' => esc_html__('Style', 'elementor-extension-kit'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('size', [
            'label' => esc_html__('Size', 'elementor-extension-kit'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'rem'],
            'range' => [
                'px' => ['min' => 24, 'max' => 320],
                'rem' => ['min' => 1.5, 'max' => 20, 'step' => 0.1],
            ],
            'default' => ['unit' => 'px', 'size' => 64],
            'selectors' => ['{{WRAPPER}} .eek-avatar' => '--eek-avatar-size: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('shape', [
            'label' => esc_html__('Shape', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'circle',
            'options' => [
                'circle' => esc_html__('Circle', 'elementor-extension-kit'),
                'rounded' => esc_html__('Rounded', 'elementor-extension-kit'),
                'square' => esc_html__('Square', 'elementor-extension-kit'),
            ],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $image = is_array($settings['image'] ?? null) ? $settings['image'] : [];
        $imageUrl = isset($image['url']) ? esc_url((string) $image['url']) : '';
        $imageId = isset($image['id']) ? absint($image['id']) : 0;
        $label = trim((string) ($settings['label'] ?? ''));
        $initials = trim((string) ($settings['initials'] ?? ''));

        $allowedShapes = ['circle', 'rounded', 'square'];
        $shape = in_array(($settings['shape'] ?? ''), $allowedShapes, true) ? (string) $settings['shape'] : 'circle';
        $allowedStatuses = ['none', 'online', 'away', 'busy', 'offline'];
        $status = in_array(($settings['status'] ?? ''), $allowedStatuses, true) ? (string) $settings['status'] : 'none';

        $alt = '';
        if ($imageId > 0) {
            $alt = trim((string) get_post_meta($imageId, '_wp_attachment_image_alt', true));
        }
        if ($alt === '') {
            $alt = $label;
        }

        if ($imageUrl === '' && $initials === '') {
            return;
        }
        ?>
        <span class="eek-avatar eek-avatar--<?php echo esc_attr($shape); ?>">
            <?php if ($imageUrl !== '') : ?>
                <img class="eek-avatar__image" src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
            <?php else : ?>
                <span class="eek-avatar__initials" role="img" aria-label="<?php echo esc_attr($label !== '' ? $label : $initials); ?>"><?php echo esc_html($initials); ?></span>
            <?php endif; ?>
            <?php if ($status !== 'none') : ?>
                <span class="eek-avatar__status eek-avatar__status--<?php echo esc_attr($status); ?>" aria-label="<?php echo esc_attr(sprintf(esc_html__('Status: %s', 'elementor-extension-kit'), $status)); ?>"></span>
            <?php endif; ?>
        </span>
        <?php
    }
}
