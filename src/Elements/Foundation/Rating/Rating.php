<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Rating;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Rating extends Widget_Base
{
    public function get_name(): string { return 'eek-rating'; }
    public function get_title(): string { return esc_html__('EEK Rating', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-rating']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('rating', ['label' => esc_html__('Rating', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'default' => 4.5, 'min' => 0, 'max' => 5, 'step' => 0.5]);
        $this->add_control('label', ['label' => esc_html__('Accessible label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Customer rating', 'elementor-extension-kit')]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $rating = max(0, min(5, (float) ($settings['rating'] ?? 0)));
        $label = trim((string) ($settings['label'] ?? ''));
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.5;
        $empty = 5 - $full - ($half ? 1 : 0);
        $aria = $label !== '' ? sprintf('%s: %.1f / 5', $label, $rating) : sprintf('%.1f / 5', $rating);
        ?>
        <div class="eek-rating" role="img" aria-label="<?php echo esc_attr($aria); ?>">
            <span aria-hidden="true"><?php echo esc_html(str_repeat('★', $full) . ($half ? '◐' : '') . str_repeat('☆', $empty)); ?></span>
        </div>
        <?php
    }
}
