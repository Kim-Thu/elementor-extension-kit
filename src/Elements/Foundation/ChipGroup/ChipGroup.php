<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Foundation\ChipGroup;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class ChipGroup extends Widget_Base
{
    public function get_name(): string { return 'eek-chip-group'; }
    public function get_title(): string { return esc_html__('EEK Chip / Tag Group', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-chip-group']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);

        $this->add_control('aria_label', [
            'label' => esc_html__('Group label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Tags', 'elementor-extension-kit'),
            'description' => esc_html__('Accessible label for the chip list.', 'elementor-extension-kit'),
        ]);

        $repeater = new Repeater();
        $repeater->add_control('label', [
            'label' => esc_html__('Label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Tag', 'elementor-extension-kit'),
        ]);
        $repeater->add_control('url', [
            'label' => esc_html__('Link', 'elementor-extension-kit'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com',
        ]);

        $this->add_control('items', [
            'label' => esc_html__('Items', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['label' => esc_html__('Design', 'elementor-extension-kit')],
                ['label' => esc_html__('Development', 'elementor-extension-kit')],
                ['label' => esc_html__('WordPress', 'elementor-extension-kit')],
            ],
            'title_field' => '{{{ label }}}',
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        $ariaLabel = trim((string) ($settings['aria_label'] ?? ''));
        $items = array_values(array_filter($items, static fn(array $item): bool => trim((string) ($item['label'] ?? '')) !== ''));

        if ($items === []) {
            return;
        }
        ?>
        <ul class="eek-chip-group"<?php if ($ariaLabel !== '') : ?> aria-label="<?php echo esc_attr($ariaLabel); ?>"<?php endif; ?>>
            <?php foreach ($items as $index => $item) :
                $label = trim((string) $item['label']);
                $url = is_array($item['url'] ?? null) ? $item['url'] : [];
                ?>
                <li class="eek-chip-group__item">
                    <?php if (! empty($url['url'])) :
                        $key = 'chip-link-' . $index;
                        $this->add_link_attributes($key, $url);
                        $this->add_render_attribute($key, 'class', 'eek-chip-group__chip eek-chip-group__chip--link');
                        ?>
                        <a <?php $this->print_render_attribute_string($key); ?>><?php echo esc_html($label); ?></a>
                    <?php else : ?>
                        <span class="eek-chip-group__chip"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}
