<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\InfoBox;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class InfoBox extends Widget_Base
{
    public function get_name(): string
    {
        return 'eek-info-box';
    }

    public function get_title(): string
    {
        return esc_html__('EEK Info Box', 'elementor-extension-kit');
    }

    public function get_icon(): string
    {
        return 'eek-brand-mark';
    }

    public function get_categories(): array
    {
        return [Plugin::ELEMENT_CATEGORY];
    }

    public function get_style_depends(): array
    {
        return ['eek-info-box'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'elementor-extension-kit'),
        ]);

        $this->add_control('media_type', [
            'label' => esc_html__('Media', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'icon',
            'options' => [
                'none' => esc_html__('None', 'elementor-extension-kit'),
                'icon' => esc_html__('Icon', 'elementor-extension-kit'),
                'image' => esc_html__('Image', 'elementor-extension-kit'),
            ],
        ]);

        $this->add_control('icon', [
            'label' => esc_html__('Icon', 'elementor-extension-kit'),
            'type' => Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-star',
                'library' => 'fa-solid',
            ],
            'condition' => ['media_type' => 'icon'],
        ]);

        $this->add_control('image', [
            'label' => esc_html__('Image', 'elementor-extension-kit'),
            'type' => Controls_Manager::MEDIA,
            'condition' => ['media_type' => 'image'],
        ]);

        $this->add_control('title', [
            'label' => esc_html__('Title', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Info box title', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('text', [
            'label' => esc_html__('Text', 'elementor-extension-kit'),
            'type' => Controls_Manager::WYSIWYG,
            'default' => esc_html__('Add supporting content for this item.', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('link_text', [
            'label' => esc_html__('Link text', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
        ]);

        $this->add_control('link', [
            'label' => esc_html__('Link', 'elementor-extension-kit'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com',
            'condition' => ['link_text!' => ''],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $mediaType = $settings['media_type'] ?? 'none';
        $title = (string) ($settings['title'] ?? '');
        $text = (string) ($settings['text'] ?? '');
        $linkText = (string) ($settings['link_text'] ?? '');
        $link = is_array($settings['link'] ?? null) ? $settings['link'] : [];

        if ($linkText !== '' && ! empty($link['url'])) {
            $this->add_link_attributes('link', $link);
            $this->add_render_attribute('link', 'class', 'eek-info-box__link');
        }
        ?>
        <article class="eek-info-box">
            <?php if ($mediaType === 'icon' && ! empty($settings['icon']['value'])) : ?>
                <div class="eek-info-box__media eek-info-box__icon" aria-hidden="true">
                    <?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                </div>
            <?php elseif ($mediaType === 'image' && ! empty($settings['image']['url'])) : ?>
                <div class="eek-info-box__media">
                    <img class="eek-info-box__image" src="<?php echo esc_url((string) $settings['image']['url']); ?>" alt="" loading="lazy">
                </div>
            <?php endif; ?>

            <div class="eek-info-box__content">
                <?php if ($title !== '') : ?>
                    <h3 class="eek-info-box__title"><?php echo esc_html($title); ?></h3>
                <?php endif; ?>

                <?php if ($text !== '') : ?>
                    <div class="eek-info-box__text"><?php echo wp_kses_post($text); ?></div>
                <?php endif; ?>

                <?php if ($linkText !== '' && ! empty($link['url'])) : ?>
                    <a <?php $this->print_render_attribute_string('link'); ?>><?php echo esc_html($linkText); ?></a>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
