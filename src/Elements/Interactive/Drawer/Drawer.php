<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\Drawer;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Drawer extends Widget_Base
{
    public function get_name(): string { return 'eek-drawer'; }
    public function get_title(): string { return esc_html__('EEK Drawer / Off-canvas', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-drawer']; }
    public function get_script_depends(): array { return ['eek-drawer']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('trigger_label', ['label' => esc_html__('Trigger label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Open drawer', 'elementor-extension-kit')]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Drawer title', 'elementor-extension-kit')]);
        $this->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Add drawer content here.', 'elementor-extension-kit')]);
        $this->add_control('side', ['label' => esc_html__('Side', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => ['left' => esc_html__('Left', 'elementor-extension-kit'), 'right' => esc_html__('Right', 'elementor-extension-kit')]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $trigger = trim((string) ($settings['trigger_label'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $content = (string) ($settings['content'] ?? '');
        $side = ($settings['side'] ?? '') === 'left' ? 'left' : 'right';
        $dialogId = 'eek-drawer-' . $this->get_id();
        $titleId = $dialogId . '-title';
        ?>
        <div class="eek-drawer" data-eek-drawer>
            <button class="eek-drawer__trigger" type="button" data-eek-drawer-open aria-haspopup="dialog" aria-controls="<?php echo esc_attr($dialogId); ?>"><?php echo esc_html($trigger !== '' ? $trigger : esc_html__('Open drawer', 'elementor-extension-kit')); ?></button>
            <dialog id="<?php echo esc_attr($dialogId); ?>" class="eek-drawer__window eek-drawer__window--<?php echo esc_attr($side); ?>"<?php if ($title !== '') : ?> aria-labelledby="<?php echo esc_attr($titleId); ?>"<?php else : ?> aria-label="<?php echo esc_attr__('Drawer', 'elementor-extension-kit'); ?>"<?php endif; ?>>
                <div class="eek-drawer__surface">
                    <button class="eek-drawer__close" type="button" data-eek-drawer-close aria-label="<?php echo esc_attr__('Close drawer', 'elementor-extension-kit'); ?>">×</button>
                    <?php if ($title !== '') : ?><h2 id="<?php echo esc_attr($titleId); ?>" class="eek-drawer__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
                    <div class="eek-drawer__content"><?php echo wp_kses_post($content); ?></div>
                </div>
            </dialog>
        </div>
        <?php
    }
}
