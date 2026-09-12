<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Collection\TeamGrid;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class TeamGrid extends Widget_Base
{
    public function get_name(): string { return 'eek-team-grid'; }
    public function get_title(): string { return esc_html__('EEK Team Grid', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-team-grid']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Team members', 'elementor-extension-kit')]);
        $repeater = new Repeater();
        $repeater->add_control('avatar', ['label' => esc_html__('Avatar', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $repeater->add_control('name', ['label' => esc_html__('Name', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Team member', 'elementor-extension-kit')]);
        $repeater->add_control('role', ['label' => esc_html__('Role', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Role', 'elementor-extension-kit')]);
        $repeater->add_control('bio', ['label' => esc_html__('Bio', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA]);
        $repeater->add_control('profile_url', ['label' => esc_html__('Profile URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);
        $this->add_control('items', ['label' => esc_html__('Items', 'elementor-extension-kit'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [], 'title_field' => '{{{ name }}}']);
        $this->end_controls_section();

        $this->start_controls_section('layout', ['label' => esc_html__('Layout', 'elementor-extension-kit'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('columns', [
            'label' => esc_html__('Columns', 'elementor-extension-kit'), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 5,
            'default' => 3, 'tablet_default' => 2, 'mobile_default' => 1,
            'selectors' => ['{{WRAPPER}} .eek-team-grid' => '--eek-team-grid-columns: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
        if ($items === []) { return; }
        ?>
        <ul class="eek-team-grid" aria-label="<?php echo esc_attr__('Team members', 'elementor-extension-kit'); ?>">
            <?php foreach ($items as $index => $item) : ?>
                <?php
                $avatarUrl = trim((string) ($item['avatar']['url'] ?? ''));
                $name = trim((string) ($item['name'] ?? ''));
                $role = trim((string) ($item['role'] ?? ''));
                $bio = trim((string) ($item['bio'] ?? ''));
                $profileUrl = is_array($item['profile_url'] ?? null) ? $item['profile_url'] : [];
                $linkKey = 'team_profile_' . (int) $index;
                if (! empty($profileUrl['url'])) { $this->add_link_attributes($linkKey, $profileUrl); }
                ?>
                <li class="eek-team-grid__item">
                    <article class="eek-team-grid__card">
                        <?php if ($avatarUrl !== '') : ?><img class="eek-team-grid__avatar" src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" decoding="async"><?php endif; ?>
                        <div class="eek-team-grid__body">
                            <?php if ($name !== '') : ?>
                                <h3 class="eek-team-grid__name">
                                    <?php if (! empty($profileUrl['url'])) : ?><a <?php $this->print_render_attribute_string($linkKey); ?>><?php echo esc_html($name); ?></a><?php else : ?><?php echo esc_html($name); ?><?php endif; ?>
                                </h3>
                            <?php endif; ?>
                            <?php if ($role !== '') : ?><p class="eek-team-grid__role"><?php echo esc_html($role); ?></p><?php endif; ?>
                            <?php if ($bio !== '') : ?><p class="eek-team-grid__bio"><?php echo nl2br(esc_html($bio)); ?></p><?php endif; ?>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}
