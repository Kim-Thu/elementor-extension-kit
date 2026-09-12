<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Progress;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Progress extends Widget_Base
{
    public function get_name(): string { return 'eek-progress'; }
    public function get_title(): string { return esc_html__('EEK Progress', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-progress']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Progress', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('value', ['label' => esc_html__('Value', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'default' => 65, 'min' => 0, 'max' => 100]);
        $this->add_control('show_value', ['label' => esc_html__('Show value', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $label = trim((string) ($settings['label'] ?? ''));
        $value = max(0, min(100, (int) ($settings['value'] ?? 0)));
        $show = ($settings['show_value'] ?? '') === 'yes';
        ?>
        <div class="eek-progress">
            <?php if ($label !== '' || $show) : ?><div class="eek-progress__meta"><?php if ($label !== '') : ?><span><?php echo esc_html($label); ?></span><?php endif; ?><?php if ($show) : ?><span><?php echo esc_html($value . '%'); ?></span><?php endif; ?></div><?php endif; ?>
            <div class="eek-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr((string) $value); ?>"<?php if ($label !== '') : ?> aria-label="<?php echo esc_attr($label); ?>"<?php endif; ?>>
                <span class="eek-progress__bar" style="width:<?php echo esc_attr((string) $value); ?>%"></span>
            </div>
        </div>
        <?php
    }
}
