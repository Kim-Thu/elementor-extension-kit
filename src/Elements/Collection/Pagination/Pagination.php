<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\Pagination;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;
use WP_Query;

final class Pagination extends Widget_Base
{
    public function get_name(): string { return 'eek-pagination'; }
    public function get_title(): string { return esc_html__('EEK Pagination', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-pagination']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Pagination', 'elementor-extension-kit')]);
        $this->add_control('prev_label', ['label' => esc_html__('Previous label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Previous', 'elementor-extension-kit')]);
        $this->add_control('next_label', ['label' => esc_html__('Next label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Next', 'elementor-extension-kit')]);
        $this->add_control('mid_size', ['label' => esc_html__('Pages around current', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 5, 'default' => 1]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        global $wp_query;

        if (! $wp_query instanceof WP_Query) {
            return;
        }

        $total = max(1, (int) $wp_query->max_num_pages);
        if ($total <= 1) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $current = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
        $midSize = max(0, min(5, (int) ($settings['mid_size'] ?? 1)));
        $prev = trim((string) ($settings['prev_label'] ?? '')) ?: esc_html__('Previous', 'elementor-extension-kit');
        $next = trim((string) ($settings['next_label'] ?? '')) ?: esc_html__('Next', 'elementor-extension-kit');

        $links = paginate_links([
            'current' => $current,
            'total' => $total,
            'mid_size' => $midSize,
            'prev_text' => $prev,
            'next_text' => $next,
            'type' => 'array',
        ]);

        if (! is_array($links) || $links === []) {
            return;
        }
        ?>
        <nav class="eek-pagination" aria-label="<?php echo esc_attr__('Pagination', 'elementor-extension-kit'); ?>">
            <ul class="eek-pagination__list">
                <?php foreach ($links as $link) : ?>
                    <li class="eek-pagination__item"><?php echo wp_kses_post((string) $link); ?></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }
}
