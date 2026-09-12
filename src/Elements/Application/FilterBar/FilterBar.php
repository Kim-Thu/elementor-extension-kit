<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\FilterBar;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class FilterBar extends Widget_Base
{
    public function get_name(): string { return 'eek-filter-bar'; }
    public function get_title(): string { return esc_html__('EEK Filter Bar', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-filter-bar']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Filters', 'elementor-extension-kit')]);
        $this->add_control('action', ['label' => esc_html__('Form action', 'elementor-extension-kit'), 'type' => Controls_Manager::URL]);
        $repeater = new Repeater();
        $repeater->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Filter', 'elementor-extension-kit')]);
        $repeater->add_control('name', ['label' => esc_html__('Query name', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => 'filter']);
        $repeater->add_control('options', ['label' => esc_html__('Options', 'elementor-extension-kit'), 'description' => esc_html__('One option per line as value|Label.', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => "all|All\nactive|Active"]);
        $this->add_control('filters', ['label' => esc_html__('Filter controls', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => []]);
        $this->add_control('submit_label', ['label' => esc_html__('Submit label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Apply filters', 'elementor-extension-kit')]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $filters = is_array($settings['filters'] ?? null) ? $settings['filters'] : [];
        $action = is_array($settings['action'] ?? null) ? trim((string) ($settings['action']['url'] ?? '')) : '';
        $submit = trim((string) ($settings['submit_label'] ?? '')) ?: esc_html__('Apply filters', 'elementor-extension-kit');
        ?>
        <form class="eek-filter-bar" method="get"<?php echo $action !== '' ? ' action="' . esc_url($action) . '"' : ''; ?>>
            <div class="eek-filter-bar__controls">
                <?php foreach ($filters as $index => $filter) :
                    $label = trim((string) ($filter['label'] ?? ''));
                    $name = sanitize_key((string) ($filter['name'] ?? ''));
                    if ($name === '') { continue; }
                    $id = 'eek-filter-' . $this->get_id() . '-' . (int) $index;
                    $lines = preg_split('/\R/', (string) ($filter['options'] ?? '')) ?: [];
                    ?>
                    <div class="eek-filter-bar__field">
                        <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label !== '' ? $label : $name); ?></label>
                        <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                            <?php foreach ($lines as $line) :
                                $parts = array_map('trim', explode('|', $line, 2));
                                $value = $parts[0] ?? '';
                                $text = $parts[1] ?? $value;
                                if ($value === '' && $text === '') { continue; }
                                ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($text); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="eek-filter-bar__submit" type="submit"><?php echo esc_html($submit); ?></button>
        </form>
        <?php
    }
}
