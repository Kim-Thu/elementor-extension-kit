<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Testimonial;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Testimonial extends Widget_Base
{
    public function get_name(): string
    {
        return 'eek-testimonial';
    }

    public function get_title(): string
    {
        return esc_html__('EEK Testimonial', 'elementor-extension-kit');
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
        return ['eek-testimonial'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'elementor-extension-kit'),
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

        $this->add_control('avatar', [
            'label' => esc_html__('Avatar', 'elementor-extension-kit'),
            'type' => Controls_Manager::MEDIA,
        ]);

        $this->add_control('rating', [
            'label' => esc_html__('Rating', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => '5',
            'options' => [
                '0' => esc_html__('None', 'elementor-extension-kit'),
                '1' => '1 / 5',
                '2' => '2 / 5',
                '3' => '3 / 5',
                '4' => '4 / 5',
                '5' => '5 / 5',
            ],
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
        $rating = (int) ($settings['rating'] ?? 0);
        $rating = max(0, min(5, $rating));

        if ($quote === '' && $author === '' && $role === '' && $avatarUrl === '' && $rating === 0) {
            return;
        }
        ?>
        <figure class="eek-testimonial">
            <?php if ($rating > 0) : ?>
                <div class="eek-testimonial__rating" role="img" aria-label="<?php echo esc_attr(sprintf(esc_html__('Rated %1$d out of %2$d', 'elementor-extension-kit'), $rating, 5)); ?>">
                    <span aria-hidden="true"><?php echo esc_html(str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($quote !== '') : ?>
                <blockquote class="eek-testimonial__quote">
                    <p><?php echo nl2br(esc_html($quote)); ?></p>
                </blockquote>
            <?php endif; ?>

            <?php if ($author !== '' || $role !== '' || $avatarUrl !== '') : ?>
                <figcaption class="eek-testimonial__author">
                    <?php if ($avatarUrl !== '') : ?>
                        <img class="eek-testimonial__avatar" src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($author); ?>" loading="lazy">
                    <?php endif; ?>

                    <span class="eek-testimonial__identity">
                        <?php if ($author !== '') : ?>
                            <strong class="eek-testimonial__name"><?php echo esc_html($author); ?></strong>
                        <?php endif; ?>
                        <?php if ($role !== '') : ?>
                            <span class="eek-testimonial__role"><?php echo esc_html($role); ?></span>
                        <?php endif; ?>
                    </span>
                </figcaption>
            <?php endif; ?>
        </figure>
        <?php
    }
}
