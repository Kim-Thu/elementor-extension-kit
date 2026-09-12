<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\PageHeader;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class PageHeader extends Widget_Base
{
    public function get_name(): string { return 'eek-page-header'; }
    public function get_title(): string { return esc_html__('EEK Page Header', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-page-header']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('meta', ['label' => esc_html__('Meta', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '', 'dynamic' => ['active' => true]]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Page title', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('description', ['label' => esc_html__('Description', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'dynamic' => ['active' => true]]);

        $repeater = new Repeater();
        $repeater->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Action', 'elementor-extension-kit')]);
        $repeater->add_control('url', ['label' => esc_html__('URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL]);
        $repeater->add_control('style', ['label' => esc_html__('Style', 'elementor-extension-kit'), 'type' => Controls_Manager::SELECT, 'default' => 'secondary', 'options' => ['primary' => esc_html__('Primary', 'elementor-extension-kit'), 'secondary' => esc_html__('Secondary', 'elementor-extension-kit')]]);
        $this->add_control('actions', ['label' => esc_html__('Actions', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => []]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $meta = trim((string) ($settings['meta'] ?? ''));
        $title = trim((string) ($settings['title'] ?? ''));
        $description = trim((string) ($settings['description'] ?? ''));
        $actions = is_array($settings['actions'] ?? null) ? $settings['actions'] : [];
        ?>
        <header class="eek-page-header">
            <div class="eek-page-header__content">
                <?php if ($meta !== '') : ?><p class="eek-page-header__meta"><?php echo esc_html($meta); ?></p><?php endif; ?>
                <?php if ($title !== '') : ?><h1 class="eek-page-header__title"><?php echo esc_html($title); ?></h1><?php endif; ?>
                <?php if ($description !== '') : ?><p class="eek-page-header__description"><?php echo nl2br(esc_html($description)); ?></p><?php endif; ?>
            </div>
            <?php if ($actions !== []) : ?>
                <div class="eek-page-header__actions" aria-label="<?php echo esc_attr__('Page actions', 'elementor-extension-kit'); ?>">
                    <?php foreach ($actions as $index => $action) :
                        $label = trim((string) ($action['label'] ?? ''));
                        $url = is_array($action['url'] ?? null) ? $action['url'] : [];
                        if ($label === '' || empty($url['url'])) { continue; }
                        $key = 'action_' . (int) $index;
                        $this->add_link_attributes($key, $url);
                        $style = ($action['style'] ?? '') === 'primary' ? 'primary' : 'secondary';
                        ?>
                        <a class="eek-page-header__action eek-page-header__action--<?php echo esc_attr($style); ?>" <?php $this->print_render_attribute_string($key); ?>><?php echo esc_html($label); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </header>
        <?php
    }
}
