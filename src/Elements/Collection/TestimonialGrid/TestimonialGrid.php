<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\TestimonialGrid;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class TestimonialGrid extends Widget_Base
{
    public function get_name(): string { return 'eek-testimonial-grid'; }
    public function get_title(): string { return esc_html__('EEK Testimonial Grid', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-testimonial-grid']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Testimonials', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('quote', ['label' => esc_html__('Quote', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('A useful testimonial.', 'elementor-extension-kit')]);
        $repeater->add_control('author', ['label' => esc_html__('Author', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Customer', 'elementor-extension-kit')]);
        $repeater->add_control('role', ['label' => esc_html__('Role / company', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT]);
        $repeater->add_control('avatar', ['label' => esc_html__('Avatar', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('rating', ['label' => esc_html__('Rating', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 5, 'step' => 1, 'default' => 5]);
        $this->add_control('items', ['label' => esc_html__('Items', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [], 'title_field' => '{{{ author }}}']);
        $this->end_controls_section();

        $this->start_controls_section('layout', ['label' => esc_html__('Layout', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('columns', [
            'label' => esc_html__('Columns', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 4,
            'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1,
            'selectors' => ['{{WRAPPER}} .eek-testimonial-grid' => '--eek-testimonial-grid-columns: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        if ($items === []) { return; }
        ?>
        <ul class="eek-testimonial-grid" aria-label="<?php echo esc_attr__('Testimonials', 'elementor-extension-kit'); ?>">
            <?php foreach ($items as $item) : ?>
                <?php
                $quote = trim((string) ($item['quote'] ?? ''));
                $author = trim((string) ($item['author'] ?? ''));
                $role = trim((string) ($item['role'] ?? ''));
                $avatarUrl = trim((string) ($item['avatar']['url'] ?? ''));
                $rating = max(0, min(5, (int) ($item['rating'] ?? 0)));
                if ($quote === '' && $author === '') { continue; }
                ?>
                <li class="eek-testimonial-grid__item">
                    <figure class="eek-testimonial-grid__card">
                        <?php if ($rating > 0) : ?>
                            <div class="eek-testimonial-grid__rating" aria-label="<?php echo esc_attr(sprintf(esc_html__('%d out of 5 stars', 'elementor-extension-kit'), $rating)); ?>"><?php echo esc_html(str_repeat('★', $rating)); ?></div>
                        <?php endif; ?>
                        <?php if ($quote !== '') : ?><blockquote class="eek-testimonial-grid__quote"><p><?php echo nl2br(esc_html($quote)); ?></p></blockquote><?php endif; ?>
                        <figcaption class="eek-testimonial-grid__author">
                            <?php if ($avatarUrl !== '') : ?><img src="<?php echo esc_url($avatarUrl); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
                            <span>
                                <?php if ($author !== '') : ?><strong><?php echo esc_html($author); ?></strong><?php endif; ?>
                                <?php if ($role !== '') : ?><small><?php echo esc_html($role); ?></small><?php endif; ?>
                            </span>
                        </figcaption>
                    </figure>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}
