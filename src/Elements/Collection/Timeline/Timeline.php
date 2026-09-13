<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\Timeline;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Timeline extends Widget_Base
{
    public function get_name(): string { return 'eek-timeline'; }
    public function get_title(): string { return esc_html__('EEK Timeline', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-timeline']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('aria_label', [
            'label' => esc_html__('Timeline label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Timeline', 'elementor-extension-kit'),
            'description' => esc_html__('Accessible label for the ordered timeline.', 'elementor-extension-kit'),
        ]);

        $repeater = new Repeater();
        $repeater->add_control('marker', [
            'label' => esc_html__('Marker label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '01',
        ]);
        $repeater->add_control('icon', [
            'label' => esc_html__('Icon', 'elementor-extension-kit'),
            'type' => Controls_Manager::ICONS,
        ]);
        $repeater->add_control('meta', [
            'label' => esc_html__('Meta', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'dynamic' => ['active' => true],
        ]);
        $repeater->add_control('title', [
            'label' => esc_html__('Title', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Timeline item', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);
        $repeater->add_control('text', [
            'label' => esc_html__('Text', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__('Describe this milestone or step.', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('items', [
            'label' => esc_html__('Items', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                ['marker' => '01', 'title' => esc_html__('Discover', 'elementor-extension-kit'), 'text' => esc_html__('Define the problem and desired outcome.', 'elementor-extension-kit')],
                ['marker' => '02', 'title' => esc_html__('Build', 'elementor-extension-kit'), 'text' => esc_html__('Create and validate the solution.', 'elementor-extension-kit')],
                ['marker' => '03', 'title' => esc_html__('Deliver', 'elementor-extension-kit'), 'text' => esc_html__('Ship the result and measure impact.', 'elementor-extension-kit')],
            ],
            'title_field' => '{{{ title }}}',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        $ariaLabel = trim((string) ($settings['aria_label'] ?? ''));

        $items = array_values(array_filter($items, static function (array $item): bool {
            return trim((string) ($item['title'] ?? '')) !== ''
                || trim((string) ($item['text'] ?? '')) !== ''
                || trim((string) ($item['meta'] ?? '')) !== ''
                || trim((string) ($item['marker'] ?? '')) !== ''
                || ! empty($item['icon']['value']);
        }));

        if ($items === []) { return; }
        ?>
        <ol class="eek-timeline"<?php if ($ariaLabel !== '') : ?> aria-label="<?php echo esc_attr($ariaLabel); ?>"<?php endif; ?>>
            <?php foreach ($items as $item) :
                $marker = trim((string) ($item['marker'] ?? ''));
                $meta = trim((string) ($item['meta'] ?? ''));
                $title = trim((string) ($item['title'] ?? ''));
                $text = trim((string) ($item['text'] ?? ''));
                $icon = is_array($item['icon'] ?? null) ? $item['icon'] : [];
                ?>
                <li class="eek-timeline__item">
                    <div class="eek-timeline__marker" aria-hidden="true">
                        <?php if (! empty($icon['value'])) { Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); } elseif ($marker !== '') { echo esc_html($marker); } ?>
                    </div>
                    <div class="eek-timeline__content">
                        <?php if ($meta !== '') : ?><div class="eek-timeline__meta"><?php echo esc_html($meta); ?></div><?php endif; ?>
                        <?php if ($title !== '') : ?><h3 class="eek-timeline__title"><?php echo esc_html($title); ?></h3><?php endif; ?>
                        <?php if ($text !== '') : ?><div class="eek-timeline__text"><?php echo nl2br(esc_html($text)); ?></div><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php
    }
}
