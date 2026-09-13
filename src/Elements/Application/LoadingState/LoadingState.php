<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\LoadingState;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class LoadingState extends Widget_Base
{
    public function get_name(): string { return 'eek-loading-state'; }
    public function get_title(): string { return esc_html__('EEK Loading State', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-loading-state']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Loading state', 'elementor-extension-kit')]);
        $this->add_control('label', [
            'label' => esc_html__('Accessible status text', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Loading content…', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);
        $this->add_control('variant', [
            'label' => esc_html__('Skeleton variant', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'list',
            'options' => [
                'list' => esc_html__('List', 'elementor-extension-kit'),
                'card' => esc_html__('Card', 'elementor-extension-kit'),
                'text' => esc_html__('Text', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('rows', [
            'label' => esc_html__('Rows', 'elementor-extension-kit'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
            'min' => 1,
            'max' => 8,
            'step' => 1,
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $label = trim((string) ($settings['label'] ?? '')) ?: esc_html__('Loading content…', 'elementor-extension-kit');
        $variant = in_array(($settings['variant'] ?? ''), ['list', 'card', 'text'], true) ? (string) $settings['variant'] : 'list';
        $rows = max(1, min(8, (int) ($settings['rows'] ?? 3)));
        ?>
        <div class="eek-loading-state eek-loading-state--<?php echo esc_attr($variant); ?>" role="status" aria-live="polite" aria-busy="true">
            <span class="eek-loading-state__status"><?php echo esc_html($label); ?></span>
            <div class="eek-loading-state__skeleton" aria-hidden="true">
                <?php for ($index = 0; $index < $rows; $index++) : ?>
                    <div class="eek-loading-state__row">
                        <?php if ($variant === 'list') : ?><span class="eek-loading-state__avatar"></span><?php endif; ?>
                        <?php if ($variant === 'card' && $index === 0) : ?><span class="eek-loading-state__media"></span><?php endif; ?>
                        <span class="eek-loading-state__lines">
                            <span class="eek-loading-state__line"></span>
                            <span class="eek-loading-state__line eek-loading-state__line--short"></span>
                        </span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php
    }
}
