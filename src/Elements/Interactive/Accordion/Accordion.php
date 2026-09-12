<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\Accordion;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Accordion extends Widget_Base
{
    public function get_name(): string { return 'eek-accordion'; }
    public function get_title(): string { return esc_html__('EEK Accordion', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-accordion']; }
    public function get_script_depends(): array { return ['eek-accordion']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Items', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Accordion item', 'elementor-extension-kit')]);
        $repeater->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Add content for this item.', 'elementor-extension-kit')]);
        $this->add_control('items', ['type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['title' => esc_html__('First item', 'elementor-extension-kit')], ['title' => esc_html__('Second item', 'elementor-extension-kit')]]]);
        $this->add_control('first_open', ['label' => esc_html__('Open first item', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('single', ['label' => esc_html__('Only one open', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = $settings['items'] ?? [];
        if (!is_array($items) || $items === []) { return; }
        $firstOpen = ($settings['first_open'] ?? '') === 'yes';
        $single = ($settings['single'] ?? '') === 'yes';
        ?>
        <div class="eek-accordion" data-eek-accordion data-single="<?php echo $single ? 'true' : 'false'; ?>">
            <?php foreach ($items as $index => $item) :
                $title = trim((string) ($item['title'] ?? ''));
                $content = (string) ($item['content'] ?? '');
                if ($title === '' && trim(wp_strip_all_tags($content)) === '') { continue; }
                $open = $firstOpen && $index === 0;
                $buttonId = 'eek-accordion-button-' . $this->get_id() . '-' . $index;
                $panelId = 'eek-accordion-panel-' . $this->get_id() . '-' . $index;
                ?>
                <div class="eek-accordion__item">
                    <h3 class="eek-accordion__heading">
                        <button id="<?php echo esc_attr($buttonId); ?>" class="eek-accordion__trigger" type="button" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($panelId); ?>">
                            <span><?php echo esc_html($title); ?></span><span aria-hidden="true">+</span>
                        </button>
                    </h3>
                    <div id="<?php echo esc_attr($panelId); ?>" class="eek-accordion__panel" role="region" aria-labelledby="<?php echo esc_attr($buttonId); ?>"<?php if (!$open) : ?> hidden<?php endif; ?>><?php echo wp_kses_post($content); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
