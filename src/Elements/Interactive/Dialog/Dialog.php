<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\Dialog;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Dialog extends Widget_Base
{
    public function get_name(): string { return 'eek-dialog'; }
    public function get_title(): string { return esc_html__('EEK Modal / Dialog', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-dialog']; }
    public function get_script_depends(): array { return ['eek-dialog']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('trigger_label', ['label' => esc_html__('Trigger label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Open dialog', 'elementor-extension-kit')]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Dialog title', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Add dialog content here.', 'elementor-extension-kit')]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $trigger = trim((string) ($settings['trigger_label'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $content = (string) ($settings['content'] ?? '');
        $dialogId = 'eek-dialog-' . $this->get_id();
        $titleId = $dialogId . '-title';
        ?>
        <div class="eek-dialog" data-eek-dialog>
            <button class="eek-dialog__trigger" type="button" data-eek-dialog-open aria-haspopup="dialog" aria-controls="<?php echo esc_attr($dialogId); ?>"><?php echo esc_html($trigger !== '' ? $trigger : esc_html__('Open dialog', 'elementor-extension-kit')); ?></button>
            <dialog id="<?php echo esc_attr($dialogId); ?>" class="eek-dialog__window"<?php if ($title !== '') : ?> aria-labelledby="<?php echo esc_attr($titleId); ?>"<?php else : ?> aria-label="<?php echo esc_attr__('Dialog', 'elementor-extension-kit'); ?>"<?php endif; ?>>
                <div class="eek-dialog__surface">
                    <button class="eek-dialog__close" type="button" data-eek-dialog-close aria-label="<?php echo esc_attr__('Close dialog', 'elementor-extension-kit'); ?>">×</button>
                    <?php if ($title !== '') : ?><h2 id="<?php echo esc_attr($titleId); ?>" class="eek-dialog__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
                    <div class="eek-dialog__content"><?php echo wp_kses_post($content); ?></div>
                </div>
            </dialog>
        </div>
        <?php
    }
}
