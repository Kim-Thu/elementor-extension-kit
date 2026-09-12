<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Application\ActivityFeed;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class ActivityFeed extends Widget_Base
{
    public function get_name(): string { return 'eek-activity-feed'; }
    public function get_title(): string { return esc_html__('EEK Activity Feed', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-activity-feed']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Activity items', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('content', ['label' => esc_html__('Content', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__('Activity happened', 'elementor-extension-kit')]);
        $repeater->add_control('time_label', ['label' => esc_html__('Time label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Just now', 'elementor-extension-kit')]);
        $repeater->add_control('datetime', ['label' => esc_html__('Machine datetime', 'elementor-extension-kit'), 'description' => esc_html__('Optional ISO-8601 value.', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $repeater->add_control('action_label', ['label' => esc_html__('Action label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $repeater->add_control('action_url', ['label' => esc_html__('Action URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL]);
        $this->add_control('items', ['label' => esc_html__('Items', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => []]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $items = $this->get_settings_for_display()['items'] ?? [];
        if (!is_array($items) || $items === []) { return; }
        ?>
        <ol class="eek-activity-feed">
            <?php foreach ($items as $index => $item) :
                $content = trim((string) ($item['content'] ?? ''));
                if ($content === '') { continue; }
                $time = trim((string) ($item['time_label'] ?? ''));
                $datetime = trim((string) ($item['datetime'] ?? ''));
                $actionLabel = trim((string) ($item['action_label'] ?? ''));
                $url = is_array($item['action_url'] ?? null) ? $item['action_url'] : [];
                ?>
                <li class="eek-activity-feed__item">
                    <div class="eek-activity-feed__body">
                        <p class="eek-activity-feed__content"><?php echo nl2br(esc_html($content)); ?></p>
                        <?php if ($time !== '') : ?><time class="eek-activity-feed__time"<?php echo $datetime !== '' ? ' datetime="' . esc_attr($datetime) . '"' : ''; ?>><?php echo esc_html($time); ?></time><?php endif; ?>
                        <?php if ($actionLabel !== '' && !empty($url['url'])) :
                            $key = 'activity_' . (int) $index;
                            $this->add_link_attributes($key, $url);
                            ?><a class="eek-activity-feed__action" <?php $this->print_render_attribute_string($key); ?>><?php echo esc_html($actionLabel); ?></a><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php
    }
}
