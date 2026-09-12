<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\EmptyState;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class EmptyState extends Widget_Base
{
    public function get_name(): string { return 'eek-empty-state'; }
    public function get_title(): string { return esc_html__('EEK Empty State', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-empty-state']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('image', ['label' => esc_html__('Image', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('title', ['label' => esc_html__('Title', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Nothing here yet', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
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
        $image = is_array($settings['image'] ?? null) ? trim((string) ($settings['image']['url'] ?? '')) : '';
        $title = trim((string) ($settings['title'] ?? ''));
        $description = trim((string) ($settings['description'] ?? ''));
        $actions = is_array($settings['actions'] ?? null) ? $settings['actions'] : [];
        ?>
        <section class="eek-empty-state" aria-labelledby="eek-empty-state-<?php echo esc_attr($this->get_id()); ?>">
            <?php if ($image !== '') : ?><img class="eek-empty-state__image" src="<?php echo esc_url($image); ?>" alt="" loading="lazy"><?php endif; ?>
            <?php if ($title !== '') : ?><h2 class="eek-empty-state__title" id="eek-empty-state-<?php echo esc_attr($this->get_id()); ?>"><?php echo esc_html($title); ?></h2><?php endif; ?>
            <?php if ($description !== '') : ?><p class="eek-empty-state__description"><?php echo nl2br(esc_html($description)); ?></p><?php endif; ?>
            <?php if ($actions !== []) : ?>
                <div class="eek-empty-state__actions">
                    <?php foreach ($actions as $index => $action) :
                        $label = trim((string) ($action['label'] ?? ''));
                        $url = is_array($action['url'] ?? null) ? $action['url'] : [];
                        if ($label === '' || empty($url['url'])) { continue; }
                        $key = 'empty_action_' . (int) $index;
                        $this->add_link_attributes($key, $url);
                        $style = ($action['style'] ?? '') === 'primary' ? 'primary' : 'secondary';
                        ?><a class="eek-empty-state__action eek-empty-state__action--<?php echo esc_attr($style); ?>" <?php $this->print_render_attribute_string($key); ?>><?php echo esc_html($label); ?></a><?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
