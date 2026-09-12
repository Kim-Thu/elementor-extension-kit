<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Toggle;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Toggle extends Widget_Base
{
    public function get_name(): string { return 'eek-toggle'; }
    public function get_title(): string { return esc_html__('EEK Toggle', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-toggle']; }
    public function get_script_depends(): array { return ['eek-toggle']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Enable option', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('checked', ['label' => esc_html__('Default state', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $label = trim((string) ($settings['label'] ?? ''));
        $checked = ($settings['checked'] ?? '') === 'yes';
        $id = 'eek-toggle-' . $this->get_id();
        ?>
        <div class="eek-toggle">
            <button id="<?php echo esc_attr($id); ?>" class="eek-toggle__control" type="button" role="switch" aria-checked="<?php echo $checked ? 'true' : 'false'; ?>"<?php if ($label !== '') : ?> aria-label="<?php echo esc_attr($label); ?>"<?php endif; ?>>
                <span class="eek-toggle__thumb" aria-hidden="true"></span>
            </button>
            <?php if ($label !== '') : ?><span class="eek-toggle__label"><?php echo esc_html($label); ?></span><?php endif; ?>
        </div>
        <?php
    }
}
