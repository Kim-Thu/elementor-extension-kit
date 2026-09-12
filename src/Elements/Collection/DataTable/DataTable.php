<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\DataTable;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class DataTable extends Widget_Base
{
    public function get_name(): string { return 'eek-data-table'; }
    public function get_title(): string { return esc_html__('EEK Data Table', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-data-table']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Table', 'elementor-extension-kit')]);
        $this->add_control('caption', [
            'label' => esc_html__('Caption', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Data table', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $columns = new Repeater();
        $columns->add_control('label', [
            'label' => esc_html__('Column heading', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Column', 'elementor-extension-kit'),
        ]);
        $this->add_control('columns', [
            'label' => esc_html__('Columns', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $columns->get_controls(),
            'default' => [
                ['label' => esc_html__('Name', 'elementor-extension-kit')],
                ['label' => esc_html__('Value', 'elementor-extension-kit')],
            ],
            'title_field' => '{{{ label }}}',
        ]);

        $this->add_control('rows', [
            'label' => esc_html__('Rows', 'elementor-extension-kit'),
            'description' => esc_html__('One row per line. Separate cells with |. Extra cells are ignored; missing cells render empty.', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => "Item A | 10\nItem B | 20",
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $columnSettings = is_array($settings['columns'] ?? null) ? $settings['columns'] : [];
        $columns = [];
        foreach ($columnSettings as $column) {
            $label = trim((string) ($column['label'] ?? ''));
            if ($label !== '') {
                $columns[] = $label;
            }
        }
        if ($columns === []) {
            return;
        }

        $rawRows = preg_split('/\R/u', (string) ($settings['rows'] ?? '')) ?: [];
        $rows = [];
        foreach ($rawRows as $rawRow) {
            if (trim($rawRow) === '') { continue; }
            $cells = array_map('trim', explode('|', $rawRow));
            $rows[] = array_slice(array_pad($cells, count($columns), ''), 0, count($columns));
        }

        $caption = trim((string) ($settings['caption'] ?? ''));
        ?>
        <div class="eek-data-table__scroll" tabindex="0" role="region" aria-label="<?php echo esc_attr($caption !== '' ? $caption : esc_html__('Scrollable data table', 'elementor-extension-kit')); ?>">
            <table class="eek-data-table">
                <?php if ($caption !== '') : ?><caption><?php echo esc_html($caption); ?></caption><?php endif; ?>
                <thead>
                    <tr><?php foreach ($columns as $column) : ?><th scope="col"><?php echo esc_html($column); ?></th><?php endforeach; ?></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr><?php foreach ($row as $cell) : ?><td><?php echo esc_html($cell); ?></td><?php endforeach; ?></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
