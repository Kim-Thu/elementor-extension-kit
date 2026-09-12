<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\Carousel;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Carousel extends Widget_Base
{
    public function get_name(): string { return 'eek-carousel'; }
    public function get_title(): string { return esc_html__('EEK Carousel / Slider', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-carousel']; }
    public function get_script_depends(): array { return ['eek-carousel']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Slides', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Slide', 'elementor-extension-kit')]);
        $repeater->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Slide content', 'elementor-extension-kit')]);
        $this->add_control('slides', ['type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['title' => esc_html__('First slide', 'elementor-extension-kit')], ['title' => esc_html__('Second slide', 'elementor-extension-kit')], ['title' => esc_html__('Third slide', 'elementor-extension-kit')]]]);
        $this->add_control('autoplay', ['label' => esc_html__('Autoplay', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('autoplay_delay', ['label' => esc_html__('Autoplay delay (ms)', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'default' => 5000, 'min' => 2000, 'step' => 500, 'condition' => ['autoplay' => 'yes']]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $slides = $settings['slides'] ?? [];
        if (!is_array($slides) || $slides === []) { return; }
        $delay = ($settings['autoplay'] ?? '') === 'yes' ? max(2000, (int) ($settings['autoplay_delay'] ?? 5000)) : 0;
        $label = esc_attr__('Carousel', 'elementor-extension-kit');
        ?>
        <section class="eek-carousel" data-eek-carousel data-autoplay="<?php echo esc_attr((string) $delay); ?>" aria-roledescription="carousel" aria-label="<?php echo $label; ?>">
            <div class="eek-carousel__viewport" data-eek-carousel-viewport tabindex="0">
                <?php $total = count($slides); foreach ($slides as $index => $slide) : ?>
                    <article class="eek-carousel__slide" data-eek-slide aria-roledescription="slide" aria-label="<?php echo esc_attr(sprintf(esc_html__('%1$d of %2$d', 'elementor-extension-kit'), $index + 1, $total)); ?>">
                        <?php $title = trim((string) ($slide['title'] ?? '')); if ($title !== '') : ?><h3 class="eek-carousel__title"><?php echo esc_html($title); ?></h3><?php endif; ?>
                        <div class="eek-carousel__content"><?php echo wp_kses_post((string) ($slide['content'] ?? '')); ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="eek-carousel__controls">
                <button type="button" data-eek-carousel-prev aria-label="<?php echo esc_attr__('Previous slide', 'elementor-extension-kit'); ?>">←</button>
                <span data-eek-carousel-status aria-live="polite"><?php echo esc_html(sprintf(esc_html__('Slide %1$d of %2$d', 'elementor-extension-kit'), 1, $total)); ?></span>
                <button type="button" data-eek-carousel-next aria-label="<?php echo esc_attr__('Next slide', 'elementor-extension-kit'); ?>">→</button>
            </div>
        </section>
        <?php
    }
}
