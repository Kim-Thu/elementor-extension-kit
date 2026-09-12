<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Interactive\Tabs;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Tabs extends Widget_Base
{
    public function get_name(): string { return 'eek-tabs'; }
    public function get_title(): string { return esc_html__('EEK Tabs', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-tabs']; }
    public function get_script_depends(): array { return ['eek-tabs']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Tabs', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('title', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Tab', 'elementor-extension-kit')]);
        $repeater->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Tab content', 'elementor-extension-kit')]);
        $this->add_control('items', ['type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['title' => esc_html__('First', 'elementor-extension-kit')], ['title' => esc_html__('Second', 'elementor-extension-kit')]]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $items = $this->get_settings_for_display('items');
        if (!is_array($items) || $items === []) { return; }
        ?>
        <div class="eek-tabs" data-eek-tabs>
            <div class="eek-tabs__list" role="tablist">
                <?php foreach ($items as $index => $item) :
                    $label = trim((string) ($item['title'] ?? ''));
                    $tabId = 'eek-tab-' . $this->get_id() . '-' . $index;
                    $panelId = 'eek-panel-' . $this->get_id() . '-' . $index;
                    ?><button id="<?php echo esc_attr($tabId); ?>" class="eek-tabs__tab" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($panelId); ?>" tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"><?php echo esc_html($label !== '' ? $label : sprintf(esc_html__('Tab %d', 'elementor-extension-kit'), $index + 1)); ?></button><?php
                endforeach; ?>
            </div>
            <?php foreach ($items as $index => $item) :
                $tabId = 'eek-tab-' . $this->get_id() . '-' . $index;
                $panelId = 'eek-panel-' . $this->get_id() . '-' . $index;
                ?><div id="<?php echo esc_attr($panelId); ?>" class="eek-tabs__panel" role="tabpanel" aria-labelledby="<?php echo esc_attr($tabId); ?>" tabindex="0"<?php if ($index !== 0) : ?> hidden<?php endif; ?>><?php echo wp_kses_post((string) ($item['content'] ?? '')); ?></div><?php
            endforeach; ?>
        </div>
        <?php
    }
}
