<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Settings\SettingsStore;
use ElementorExtensionKit\Elementor\ElementorRegionTemplateRepository;
use ElementorExtensionKit\Elementor\SiteRegionAssignmentResolver;

final class SiteRegionsPanel
{
    public static function render(): void
    {
        $settings = SettingsStore::get();
        $regions = is_array($settings['regions'] ?? null) ? $settings['regions'] : [];

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="eek_save_global_settings">';
        echo '<input type="hidden" name="eek_section" value="regions">';
        wp_nonce_field('eek_save_global_settings');

        self::renderRegion('header', __('Header', 'elementor-extension-kit'), $regions['header'] ?? null);

        echo '<div class="eek-settings__actions">';
        EffectiveConfiguration::renderDirtyStatus(false, __('Default leaves Elementor or the active theme in control.', 'elementor-extension-kit'));
        submit_button(__('Save site regions', 'elementor-extension-kit'), 'primary', 'submit', false);
        echo '</div></form>';
    }

    private static function renderRegion(string $region, string $label, mixed $stored): void
    {
        $templates = ElementorRegionTemplateRepository::forRegion($region);
        $options = ['default' => __('Default / Elementor native', 'elementor-extension-kit')];
        foreach ($templates as $postId => $title) {
            $options[(string) $postId] = $title;
        }

        $value = is_numeric($stored) ? (string) (int) $stored : 'default';
        $effective = SiteRegionAssignmentResolver::resolve($region);

        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(
            $label,
            __('Choose a saved Elementor region template once, or keep native theme/Elementor behavior untouched.', 'elementor-extension-kit'),
            static function () use ($region, $options, $value, $effective): void {
                SettingsControls::select('eek[regions][' . $region . ']', 'eek-region-' . $region, $options, $value);
                EffectiveConfiguration::renderState(
                    $effective === null ? 'default' : 'eek-global',
                    $effective === null ? __('Elementor / theme native', 'elementor-extension-kit') : (string) ($options[(string) $effective] ?? $effective)
                );
            }
        );
        echo '</div></section>';
    }
}
