<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Card;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Elementor\ElementInstanceInheritance;
use ElementorExtensionKit\Elementor\ElementTemplateRegistry;
use ElementorExtensionKit\Elementor\ElementTemplateResolver;
use WP_Post;

final class Card extends Widget_Base
{
    public function get_name(): string { return 'eek-card'; }
    public function get_title(): string { return esc_html__('Card', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eicon-inner-section eek-widget-icon'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-card']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content & Data', 'elementor-extension-kit')]);
        $this->add_control('data_source', [
            'label' => esc_html__('Data source', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'manual',
            'options' => [
                'manual' => esc_html__('Manual', 'elementor-extension-kit'),
                'current' => esc_html__('Current Post / CPT', 'elementor-extension-kit'),
                'selected' => esc_html__('Selected Post / CPT', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('post_id', [
            'label' => esc_html__('Post / CPT ID', 'elementor-extension-kit'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'step' => 1,
            'description' => esc_html__('Enter the ID of a published post, page or public CPT. This setting does not perform a listing query.', 'elementor-extension-kit'),
            'condition' => ['data_source' => 'selected'],
        ]);
        $this->add_control('title', [
            'label' => esc_html__('Title', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Card title', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
            'condition' => ['data_source' => 'manual'],
        ]);
        $this->add_control('description', [
            'label' => esc_html__('Description', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__('Card description.', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
            'condition' => ['data_source' => 'manual'],
        ]);
        $this->add_control('manual_image', [
            'label' => esc_html__('Image', 'elementor-extension-kit'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => ['active' => true],
            'condition' => ['data_source' => 'manual'],
        ]);
        $this->add_control('manual_link', [
            'label' => esc_html__('Link', 'elementor-extension-kit'),
            'type' => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'condition' => ['data_source' => 'manual'],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('mapping', [
            'label' => esc_html__('Post Field Mapping', 'elementor-extension-kit'),
            'condition' => ['data_source!' => 'manual'],
        ]);
        $this->add_control('title_field', [
            'label' => esc_html__('Title field', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT, 'default' => 'title',
            'options' => ['title' => esc_html__('Post title', 'elementor-extension-kit'), 'meta' => esc_html__('Custom field', 'elementor-extension-kit'), 'none' => esc_html__('Hide', 'elementor-extension-kit')],
        ]);
        $this->add_control('title_meta', [
            'label' => esc_html__('Title custom field key', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'condition' => ['title_field' => 'meta'],
        ]);
        $this->add_control('description_field', [
            'label' => esc_html__('Description field', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT, 'default' => 'excerpt',
            'options' => [
                'excerpt' => esc_html__('Excerpt (fallback to content)', 'elementor-extension-kit'),
                'content' => esc_html__('Post content', 'elementor-extension-kit'),
                'meta' => esc_html__('Custom field', 'elementor-extension-kit'),
                'none' => esc_html__('Hide', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('description_meta', [
            'label' => esc_html__('Description custom field key', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'condition' => ['description_field' => 'meta'],
        ]);
        $this->add_control('image_field', [
            'label' => esc_html__('Image field', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT, 'default' => 'featured',
            'options' => [
                'featured' => esc_html__('Featured image', 'elementor-extension-kit'),
                'meta' => esc_html__('Custom field (attachment ID)', 'elementor-extension-kit'),
                'none' => esc_html__('Hide', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('image_meta', [
            'label' => esc_html__('Image custom field key', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'condition' => ['image_field' => 'meta'],
        ]);
        $this->add_control('link_field', [
            'label' => esc_html__('Link field', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT, 'default' => 'permalink',
            'options' => [
                'permalink' => esc_html__('Permalink', 'elementor-extension-kit'),
                'meta' => esc_html__('Custom URL field', 'elementor-extension-kit'),
                'none' => esc_html__('No link', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('link_meta', [
            'label' => esc_html__('Link custom field key', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'condition' => ['link_field' => 'meta'],
        ]);
        $this->add_control('show_date', [
            'label' => esc_html__('Show publish date', 'elementor-extension-kit'),
            'type' => Controls_Manager::SWITCHER, 'default' => '',
        ]);
        $this->end_controls_section();

        $this->start_controls_section('presentation', ['label' => esc_html__('Template', 'elementor-extension-kit')]);
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
        $this->end_controls_section();

        $this->start_controls_section('card_style', [
            'label' => esc_html__('Card', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('card_gap', [
            'label' => esc_html__('Content gap (inherit when empty)', 'elementor-extension-kit'),
            'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'rem', 'em'],
            'range' => ['px' => ['min' => 0, 'max' => 100], 'rem' => ['min' => 0, 'max' => 8], 'em' => ['min' => 0, 'max' => 8]],
            'selectors' => ['{{WRAPPER}} .eek-card' => '--eek-card-local-gap: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('card_padding', [
            'label' => esc_html__('Padding (inherit when empty)', 'elementor-extension-kit'),
            'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'rem', 'em'],
            'selectors' => ['{{WRAPPER}} .eek-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('card_radius', [
            'label' => esc_html__('Radius (inherit when empty)', 'elementor-extension-kit'),
            'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'rem'],
            'range' => ['px' => ['min' => 0, 'max' => 100], 'rem' => ['min' => 0, 'max' => 6]],
            'selectors' => ['{{WRAPPER}} .eek-card' => 'border-radius: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('card_background', [
            'label' => esc_html__('Background (inherit when empty)', 'elementor-extension-kit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .eek-card' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('card_border_color', [
            'label' => esc_html__('Border color', 'elementor-extension-kit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .eek-card' => 'border-color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('card_border_width', [
            'label' => esc_html__('Border width', 'elementor-extension-kit'),
            'type' => Controls_Manager::SLIDER, 'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 12]],
            'selectors' => ['{{WRAPPER}} .eek-card' => 'border-width: {{SIZE}}px;'],
        ]);
        $this->add_control('card_shadow', [
            'label' => esc_html__('Shadow', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => esc_html__('Inherit global', 'elementor-extension-kit'),
                'none' => esc_html__('None', 'elementor-extension-kit'),
                'var(--eek-shadow-sm)' => esc_html__('Small', 'elementor-extension-kit'),
                'var(--eek-shadow-md)' => esc_html__('Medium', 'elementor-extension-kit'),
                'var(--eek-shadow-lg)' => esc_html__('Large', 'elementor-extension-kit'),
            ],
            'default' => '',
            'selectors' => ['{{WRAPPER}} .eek-card' => 'box-shadow: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        $this->start_controls_section('card_text_style', [
            'label' => esc_html__('Text & Link', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('title_color', [
            'label' => esc_html__('Title color', 'elementor-extension-kit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .eek-card__title' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .eek-card__title',
        ]);
        $this->add_control('description_color', [
            'label' => esc_html__('Description color', 'elementor-extension-kit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .eek-card__description' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'description_typography',
            'selector' => '{{WRAPPER}} .eek-card__description',
        ]);
        $this->add_control('link_color', [
            'label' => esc_html__('Link color', 'elementor-extension-kit'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .eek-card__link' => 'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $source = (string) ($settings['data_source'] ?? 'manual');
        $card = [
            'title' => (string) ($settings['title'] ?? ''),
            'description' => (string) ($settings['description'] ?? ''),
            'image' => '',
            'url' => '',
            'date' => '',
            'external' => false,
            'nofollow' => false,
        ];

        if ($source === 'manual') {
            $media = $settings['manual_image'] ?? [];
            $id = is_array($media) ? absint($media['id'] ?? 0) : 0;
            if ($id > 0 && wp_attachment_is_image($id)) {
                $card['image'] = wp_get_attachment_image($id, 'large', false, ['class' => 'eek-card__image', 'loading' => 'lazy']) ?: '';
            } elseif (is_array($media) && ! empty($media['url'])) {
                $card['image'] = '<img class="eek-card__image" loading="lazy" src="' . esc_url((string) $media['url']) . '" alt="">';
            }
            $link = is_array($settings['manual_link'] ?? null) ? $settings['manual_link'] : [];
            $card['url'] = esc_url((string) ($link['url'] ?? ''));
            $card['external'] = ! empty($link['is_external']);
            $card['nofollow'] = ! empty($link['nofollow']);
        } elseif (in_array($source, ['current', 'selected'], true)) {
            $post = $source === 'current' ? get_post() : get_post(absint($settings['post_id'] ?? 0));
            if (! $post instanceof WP_Post || ! self::canDisplayPost($post)) {
                // Do not render unpublished or non-public content to anonymous visitors.
                return;
            }
            $card = self::postData($post, $settings);
        }

        $local = ElementInstanceInheritance::local($settings, 'template');
        $template = ElementTemplateResolver::resolve('card', $local['value'], $local['has_local']);
        $path = is_array($template) && is_string($template['path'] ?? null)
            ? $template['path']
            : __DIR__ . '/templates/cardDefault.php';
        if (is_readable($path)) {
            require $path;
        }
    }

    private static function canDisplayPost(WP_Post $post): bool
    {
        $type = get_post_type_object($post->post_type);
        if (! $type || (! $type->public && ! $type->publicly_queryable)) {
            return false;
        }
        return $post->post_status === 'publish';
    }

    private static function mappedMeta(WP_Post $post, mixed $key): string
    {
        $key = is_string($key) ? trim($key) : '';
        // Never expose WordPress protected meta keys through an arbitrary mapping.
        if ($key === '' || str_starts_with($key, '_') || ! preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            return '';
        }
        $value = get_post_meta($post->ID, $key, true);
        return is_scalar($value) ? (string) $value : '';
    }

    private static function postData(WP_Post $post, array $settings): array
    {
        $titleField = (string) ($settings['title_field'] ?? 'title');
        $descriptionField = (string) ($settings['description_field'] ?? 'excerpt');
        $imageField = (string) ($settings['image_field'] ?? 'featured');
        $linkField = (string) ($settings['link_field'] ?? 'permalink');
        $title = $titleField === 'none' ? '' : ($titleField === 'meta'
            ? self::mappedMeta($post, $settings['title_meta'] ?? '')
            : get_the_title($post));
        $description = '';
        if ($descriptionField === 'meta') {
            $description = self::mappedMeta($post, $settings['description_meta'] ?? '');
        } elseif ($descriptionField === 'content') {
            $description = $post->post_content;
        } elseif ($descriptionField === 'excerpt') {
            $description = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 35);
        }

        $imageId = $imageField === 'featured' ? get_post_thumbnail_id($post) : 0;
        if ($imageField === 'meta') {
            $imageId = absint(self::mappedMeta($post, $settings['image_meta'] ?? ''));
        }
        $image = $imageId > 0 && wp_attachment_is_image($imageId)
            ? (wp_get_attachment_image($imageId, 'large', false, ['class' => 'eek-card__image', 'loading' => 'lazy']) ?: '')
            : '';
        $url = $linkField === 'meta'
            ? self::mappedMeta($post, $settings['link_meta'] ?? '')
            : ($linkField === 'none' ? '' : get_permalink($post));
        return [
            'title' => wp_strip_all_tags((string) $title),
            'description' => $description,
            'image' => $image,
            'url' => esc_url((string) $url),
            'date' => ! empty($settings['show_date']) ? get_the_date('', $post) : '',
            'external' => false,
            'nofollow' => false,
        ];
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
