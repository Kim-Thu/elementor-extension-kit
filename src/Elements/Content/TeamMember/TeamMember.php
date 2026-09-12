<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\TeamMember;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use ElementorExtensionKit\Core\Plugin;

final class TeamMember extends Widget_Base
{
    public function get_name(): string
    {
        return 'eek-team-member';
    }

    public function get_title(): string
    {
        return esc_html__('EEK Team Member', 'elementor-extension-kit');
    }

    public function get_icon(): string
    {
        return 'eek-brand-mark';
    }

    public function get_categories(): array
    {
        return [Plugin::ELEMENT_CATEGORY];
    }

    public function get_style_depends(): array
    {
        return ['eek-team-member'];
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content', [
            'label' => esc_html__('Content', 'elementor-extension-kit'),
        ]);

        $this->add_control('avatar', [
            'label' => esc_html__('Avatar', 'elementor-extension-kit'),
            'type' => Controls_Manager::MEDIA,
        ]);

        $this->add_control('name', [
            'label' => esc_html__('Name', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Team member', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('role', [
            'label' => esc_html__('Role', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Role', 'elementor-extension-kit'),
            'dynamic' => ['active' => true],
        ]);

        $this->add_control('bio', [
            'label' => esc_html__('Bio', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '',
            'dynamic' => ['active' => true],
        ]);

        $repeater = new Repeater();
        $repeater->add_control('label', [
            'label' => esc_html__('Label', 'elementor-extension-kit'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Profile', 'elementor-extension-kit'),
        ]);
        $repeater->add_control('url', [
            'label' => esc_html__('URL', 'elementor-extension-kit'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://example.com',
        ]);

        $this->add_control('social_links', [
            'label' => esc_html__('Social links', 'elementor-extension-kit'),
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
        $avatarUrl = (string) ($settings['avatar']['url'] ?? '');
        $name = trim((string) ($settings['name'] ?? ''));
        $role = trim((string) ($settings['role'] ?? ''));
        $bio = trim((string) ($settings['bio'] ?? ''));
        $socialLinks = is_array($settings['social_links'] ?? null) ? $settings['social_links'] : [];
        ?>
        <article class="eek-team-member">
            <?php if ($avatarUrl !== '') : ?>
                <img class="eek-team-member__avatar" src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
            <?php endif; ?>

            <div class="eek-team-member__content">
                <?php if ($name !== '') : ?>
                    <h3 class="eek-team-member__name"><?php echo esc_html($name); ?></h3>
                <?php endif; ?>

                <?php if ($role !== '') : ?>
                    <p class="eek-team-member__role"><?php echo esc_html($role); ?></p>
                <?php endif; ?>

                <?php if ($bio !== '') : ?>
                    <p class="eek-team-member__bio"><?php echo nl2br(esc_html($bio)); ?></p>
                <?php endif; ?>

                <?php if ($socialLinks !== []) : ?>
                    <ul class="eek-team-member__socials" aria-label="<?php echo esc_attr__('Social links', 'elementor-extension-kit'); ?>">
                        <?php foreach ($socialLinks as $index => $social) : ?>
                            <?php
                            $label = trim((string) ($social['label'] ?? ''));
                            $url = is_array($social['url'] ?? null) ? $social['url'] : [];
                            if ($label === '' || empty($url['url'])) {
                                continue;
                            }
                            $attributeKey = 'social_' . (int) $index;
                            $this->add_link_attributes($attributeKey, $url);
                            ?>
                            <li><a <?php $this->print_render_attribute_string($attributeKey); ?>><?php echo esc_html($label); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
