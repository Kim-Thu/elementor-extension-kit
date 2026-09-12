<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\TooltipPopover;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class TooltipPopover extends Widget_Base
{
    public function get_name(): string { return 'eek-tooltip-popover'; }
    public function get_title(): string { return esc_html__('EEK Tooltip / Popover', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-tooltip-popover']; }
    public function get_script_depends(): array { return ['eek-tooltip-popover']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('mode', ['label' => esc_html__('Mode', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'tooltip', 'options' => ['tooltip' => esc_html__('Tooltip', 'elementor-extension-kit'), 'popover' => esc_html__('Popover', 'elementor-extension-kit')]]);
        $this->add_control('trigger', ['label' => esc_html__('Trigger label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('More info', 'elementor-extension-kit')]);
        $this->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('Helpful contextual information.', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $mode = ($settings['mode'] ?? '') === 'popover' ? 'popover' : 'tooltip';
        $trigger = trim((string) ($settings['trigger'] ?? ''));
        $content = trim((string) ($settings['content'] ?? ''));
        if ($trigger === '' || $content === '') { return; }
        $id = 'eek-overlay-' . $this->get_id();
        if ($mode === 'popover') {
            ?>
            <span class="eek-overlay eek-overlay--popover">
                <button class="eek-overlay__trigger" type="button" popovertarget="<?php echo esc_attr($id); ?>" aria-haspopup="dialog"><?php echo esc_html($trigger); ?></button>
                <span id="<?php echo esc_attr($id); ?>" class="eek-overlay__popover" popover role="dialog" aria-label="<?php echo esc_attr($trigger); ?>"><?php echo nl2br(esc_html($content)); ?></span>
            </span>
            <?php
            return;
        }
        ?>
        <span class="eek-overlay eek-overlay--tooltip" data-eek-tooltip>
            <button class="eek-overlay__trigger" type="button" aria-describedby="<?php echo esc_attr($id); ?>"><?php echo esc_html($trigger); ?></button>
            <span id="<?php echo esc_attr($id); ?>" class="eek-overlay__tooltip" role="tooltip" hidden><?php echo nl2br(esc_html($content)); ?></span>
        </span>
        <?php
    }
}
