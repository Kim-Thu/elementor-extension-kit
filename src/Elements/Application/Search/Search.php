<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\Search;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class Search extends Widget_Base
{
    public function get_name(): string { return 'eek-search'; }
    public function get_title(): string { return esc_html__('EEK Search', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-search']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Search', 'elementor-extension-kit')]);
        $this->add_control('label', ['label' => esc_html__('Accessible label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Search', 'elementor-extension-kit')]);
        $this->add_control('placeholder', ['label' => esc_html__('Placeholder', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Search…', 'elementor-extension-kit')]);
        $this->add_control('name', ['label' => esc_html__('Query name', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => 's']);
        $this->add_control('action', ['label' => esc_html__('Form action', 'elementor-extension-kit'), 'type' => Controls_Manager::URL]);
        $this->add_control('button_label', ['label' => esc_html__('Button label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Search', 'elementor-extension-kit')]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $label = trim((string) ($settings['label'] ?? '')) ?: esc_html__('Search', 'elementor-extension-kit');
        $placeholder = trim((string) ($settings['placeholder'] ?? ''));
        $name = sanitize_key((string) ($settings['name'] ?? 's')) ?: 's';
        $button = trim((string) ($settings['button_label'] ?? '')) ?: esc_html__('Search', 'elementor-extension-kit');
        $action = is_array($settings['action'] ?? null) ? trim((string) ($settings['action']['url'] ?? '')) : '';
        $id = 'eek-search-' . $this->get_id();
        ?>
        <form class="eek-search" role="search" method="get"<?php echo $action !== '' ? ' action="' . esc_url($action) . '"' : ''; ?>>
            <label class="eek-search__label" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
            <div class="eek-search__row">
                <input class="eek-search__input" id="<?php echo esc_attr($id); ?>" type="search" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
                <button class="eek-search__button" type="submit"><?php echo esc_html($button); ?></button>
            </div>
        </form>
        <?php
    }
}
