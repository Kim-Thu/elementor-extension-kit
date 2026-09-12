<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\MediaCard;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class MediaCard extends Widget_Base
{
    public function get_name(): string { return 'eek-media-card'; }
    public function get_title(): string { return esc_html__('EEK Media Card', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-media-card']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('media_type', [
            'label' => esc_html__('Media type', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'image',
            'options' => [
                'image' => esc_html__('Image', 'elementor-extension-kit'),
                'video' => esc_html__('Video', 'elementor-extension-kit'),
                'none' => esc_html__('None', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('image', ['label' => esc_html__('Image', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('video_url', ['label' => esc_html__('Video URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com/video.mp4']);
        $this->add_control('meta', ['label' => esc_html__('Meta', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Category · 5 min read', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Media card title', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('text', ['label' => esc_html__('Text', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'dynamic' => ['active' => true]]);
        $this->add_control('action_label', ['label' => esc_html__('Action label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Read more', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('action_url', ['label' => esc_html__('Action URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $mediaType = in_array(($settings['media_type'] ?? ''), ['image', 'video', 'none'], true) ? (string) $settings['media_type'] : 'image';
        $imageUrl = trim((string) ($settings['image']['url'] ?? ''));
        $video = is_array($settings['video_url'] ?? null) ? $settings['video_url'] : [];
        $videoUrl = trim((string) ($video['url'] ?? ''));
        $meta = trim((string) ($settings['meta'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $text = trim((string) ($settings['text'] ?? ''));
        $actionLabel = trim((string) ($settings['action_label'] ?? ''));
        $actionUrl = is_array($settings['action_url'] ?? null) ? $settings['action_url'] : [];
        ?>
        <article class="eek-media-card">
            <?php if ($mediaType === 'image' && $imageUrl !== '') : ?>
                <div class="eek-media-card__media">
                    <img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
                </div>
            <?php elseif ($mediaType === 'video' && $videoUrl !== '') : ?>
                <div class="eek-media-card__media">
                    <video controls preload="metadata" aria-label="<?php echo esc_attr($title !== '' ? $title : esc_html__('Media', 'elementor-extension-kit')); ?>">
                        <source src="<?php echo esc_url($videoUrl); ?>">
                    </video>
                </div>
            <?php endif; ?>

            <div class="eek-media-card__body">
                <?php if ($meta !== '') : ?><p class="eek-media-card__meta"><?php echo esc_html($meta); ?></p><?php endif; ?>
                <?php if ($title !== '') : ?><h3 class="eek-media-card__title"><?php echo esc_html($title); ?></h3><?php endif; ?>
                <?php if ($text !== '') : ?><p class="eek-media-card__text"><?php echo nl2br(esc_html($text)); ?></p><?php endif; ?>
                <?php if ($actionLabel !== '' && ! empty($actionUrl['url'])) : ?>
                    <?php $this->add_link_attributes('action', $actionUrl); ?>
                    <a class="eek-media-card__action" <?php $this->print_render_attribute_string('action'); ?>><?php echo esc_html($actionLabel); ?></a>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
