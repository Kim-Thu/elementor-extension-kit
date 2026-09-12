<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\Breadcrumb;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Breadcrumb extends Widget_Base
{
    public function get_name(): string { return 'eek-breadcrumb'; }
    public function get_title(): string { return esc_html__('EEK Breadcrumb', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-breadcrumb']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Items', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Page', 'elementor-extension-kit')]);
        $repeater->add_control('url', ['label' => esc_html__('Link', 'elementor-extension-kit'), 'type' => Controls_Manager::URL]);
        $repeater->add_control('current', ['label' => esc_html__('Current page', 'elementor-extension-kit'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('items', ['label' => esc_html__('Breadcrumb items', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['label' => esc_html__('Home', 'elementor-extension-kit')], ['label' => esc_html__('Current page', 'elementor-extension-kit'), 'current' => 'yes']]]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $items = $this->get_settings_for_display('items');
        if (!is_array($items) || $items === []) { return; }
        ?>
        <nav class="eek-breadcrumb" aria-label="<?php echo esc_attr__('Breadcrumb', 'elementor-extension-kit'); ?>">
            <ol class="eek-breadcrumb__list">
                <?php foreach ($items as $index => $item) :
                    $label = trim((string) ($item['label'] ?? ''));
                    if ($label === '') { continue; }
                    $current = ($item['current'] ?? '') === 'yes';
                    $url = (string) ($item['url']['url'] ?? '');
                    ?>
                    <li class="eek-breadcrumb__item">
                        <?php if (!$current && $url !== '') :
                            $key = 'link_' . $index;
                            $this->add_link_attributes($key, $item['url']);
                            ?><a <?php echo $this->get_render_attribute_string($key); ?>><?php echo esc_html($label); ?></a><?php
                        else : ?>
                            <span<?php if ($current) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
    }
}
