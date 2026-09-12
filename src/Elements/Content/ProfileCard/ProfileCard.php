<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\ProfileCard;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class ProfileCard extends Widget_Base
{
    public function get_name(): string { return 'eek-profile-card'; }
    public function get_title(): string { return esc_html__('EEK Profile Card', 'elementor-extension-kit'); }
    public function get_icon(): string { return 'eek-brand-mark'; }
    public function get_categories(): array { return [Plugin::ELEMENT_CATEGORY]; }
    public function get_style_depends(): array { return ['eek-profile-card']; }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', ['label' => esc_html__('Content', 'elementor-extension-kit')]);
        $this->add_control('avatar', ['label' => esc_html__('Avatar', 'elementor-extension-kit'), 'type' => Controls_Manager::MEDIA]);
        $this->add_control('name', ['label' => esc_html__('Name', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Profile name', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('meta', ['label' => esc_html__('Meta', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Account · Member', 'elementor-extension-kit'), 'dynamic' => ['active' => true]]);
        $this->add_control('bio', ['label' => esc_html__('Bio', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXTAREA, 'default' => '', 'dynamic' => ['active' => true]]);

        $repeater = new Repeater();
        $repeater->add_control('label', ['label' => esc_html__('Label', 'elementor-extension-kit'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('View profile', 'elementor-extension-kit')]);
        $repeater->add_control('url', ['label' => esc_html__('URL', 'elementor-extension-kit'), 'type' => Controls_Manager::URL, 'placeholder' => 'https://example.com']);
        $repeater->add_control('style', [
            'label' => esc_html__('Style', 'elementor-extension-kit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'primary',
            'options' => [
                'primary' => esc_html__('Primary', 'elementor-extension-kit'),
                'secondary' => esc_html__('Secondary', 'elementor-extension-kit'),
                'link' => esc_html__('Link', 'elementor-extension-kit'),
            ],
        ]);
        $this->add_control('actions', [
            'label' => esc_html__('Actions', 'elementor-extension-kit'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ label }}}',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $avatarUrl = trim((string) ($settings['avatar']['url'] ?? ''));
        $name = trim((string) ($settings['name'] ?? ''));
        $meta = trim((string) ($settings['meta'] ?? ''));
        $bio = trim((string) ($settings['bio'] ?? ''));
        $actions = is_array($settings['actions'] ?? null) ? $settings['actions'] : [];
        $allowedStyles = ['primary', 'secondary', 'link'];
        ?>
        <article class="eek-profile-card">
            <?php if ($avatarUrl !== '') : ?>
                <img class="eek-profile-card__avatar" src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" decoding="async">
            <?php endif; ?>

            <div class="eek-profile-card__body">
                <?php if ($name !== '') : ?>
                    <h3 class="eek-profile-card__name"><?php echo esc_html($name); ?></h3>
                <?php endif; ?>

                <?php if ($meta !== '') : ?>
                    <p class="eek-profile-card__meta"><?php echo esc_html($meta); ?></p>
                <?php endif; ?>

                <?php if ($bio !== '') : ?>
                    <p class="eek-profile-card__bio"><?php echo nl2br(esc_html($bio)); ?></p>
                <?php endif; ?>

                <?php if ($actions !== []) : ?>
                    <div class="eek-profile-card__actions" aria-label="<?php echo esc_attr__('Profile actions', 'elementor-extension-kit'); ?>">
                        <?php foreach ($actions as $index => $action) : ?>
                            <?php
                            $label = trim((string) ($action['label'] ?? ''));
                            $url = is_array($action['url'] ?? null) ? $action['url'] : [];
                            if ($label === '' || empty($url['url'])) { continue; }
                            $style = in_array(($action['style'] ?? ''), $allowedStyles, true) ? (string) $action['style'] : 'primary';
                            $attributeKey = 'profile_action_' . (int) $index;
                            $this->add_link_attributes($attributeKey, $url);
                            $this->add_render_attribute($attributeKey, 'class', 'eek-profile-card__action eek-profile-card__action--' . $style);
                            ?>
                            <a <?php $this->print_render_attribute_string($attributeKey); ?>><?php echo esc_html($label); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
