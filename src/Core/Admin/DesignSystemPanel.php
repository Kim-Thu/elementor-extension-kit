<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Settings\SemanticDesignSettings;
use ElementorExtensionKit\Core\Settings\SettingsStore;
use ElementorExtensionKit\Elementor\ElementorGlobalSource;

final class DesignSystemPanel
{
    public static function render(): void
    {
        $settings = SettingsStore::get();
        $design = isset($settings['design']) && is_array($settings['design']) ? $settings['design'] : [];
        $controls = SemanticDesignSettings::controls();

        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(
            __('Global Colors & Typography', 'elementor-extension-kit'),
            __('Owned by the active Elementor Kit. EEK references them instead of duplicating them.', 'elementor-extension-kit'),
            static function (): void {
                printf(
                    '<a class="button button-secondary" href="%s">%s</a>',
                    esc_url(ElementorGlobalSource::siteSettingsUrl()),
                    esc_html__('Open Elementor Site Settings', 'elementor-extension-kit')
                );
                EffectiveConfiguration::renderState('elementor', __('Native source of truth', 'elementor-extension-kit'));
            }
        );
        echo '</div></section>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="eek_save_global_settings">';
        echo '<input type="hidden" name="eek_section" value="design-system">';
        wp_nonce_field('eek_save_global_settings');

        foreach ($controls as $group => $groupControls) {
            echo '<section class="eek-settings__section">';
            echo '<div class="eek-settings__section-head"><div>';
            printf('<h3 class="eek-settings__section-title">%s</h3>', esc_html(self::groupLabel($group)));
            printf('<p class="eek-settings__section-copy">%s</p>', esc_html(self::groupDescription($group)));
            echo '</div></div><div class="eek-settings__section-body">';

            foreach ($groupControls as $key => $control) {
                self::renderControl($group, $key, $control, $design);
            }

            echo '</div></section>';
        }

        echo '<div class="eek-settings__actions">';
        EffectiveConfiguration::renderDirtyStatus(false, __('Global design values inherit until you save an override.', 'elementor-extension-kit'));
        submit_button(__('Save global design', 'elementor-extension-kit'), 'primary', 'submit', false);
        echo '</div></form>';
    }

    /**
     * @param array<string, mixed> $control
     * @param array<string, mixed> $design
     */
    private static function renderControl(string $group, string $key, array $control, array $design): void
    {
        $id = 'eek-' . sanitize_html_class($group . '-' . $key);
        $name = 'eek[design][' . $group . '][' . $key . ']';
        $stored = isset($design[$group]) && is_array($design[$group]) ? ($design[$group][$key] ?? null) : null;

        SettingsControls::field(
            (string) $control['label'],
            sprintf(__('Default: %s%s. Leave blank to inherit the EEK baseline.', 'elementor-extension-kit'), (string) $control['default'], (string) ($control['unit'] ?? '')),
            static function () use ($control, $name, $id, $stored): void {
                if (($control['type'] ?? '') === 'select') {
                    SettingsControls::select($name, $id, (array) $control['options'], is_scalar($stored) ? (string) $stored : (string) $control['default']);
                } else {
                    SettingsControls::number(
                        $name,
                        $id,
                        is_numeric($stored) ? (float) $stored : null,
                        (float) $control['min'],
                        (float) $control['max'],
                        (float) $control['step']
                    );
                    if (isset($control['unit'])) {
                        echo ' <span class="eek-badge">' . esc_html((string) $control['unit']) . '</span>';
                    }
                }
                EffectiveConfiguration::renderState($stored === null ? 'default' : 'eek-global', $stored === null ? __('Inherited baseline', 'elementor-extension-kit') : (string) $stored);
            },
            $id
        );
    }

    private static function groupLabel(string $group): string
    {
        return match ($group) {
            'spacing' => __('Spacing', 'elementor-extension-kit'),
            'radius' => __('Radius', 'elementor-extension-kit'),
            'border' => __('Borders', 'elementor-extension-kit'),
            'shadow' => __('Elevation', 'elementor-extension-kit'),
            'surface' => __('Surfaces', 'elementor-extension-kit'),
            'motion' => __('Motion', 'elementor-extension-kit'),
            default => ucfirst($group),
        };
    }

    private static function groupDescription(string $group): string
    {
        return match ($group) {
            'spacing' => __('Control the rhythm shared by EEK components and sections.', 'elementor-extension-kit'),
            'radius' => __('Control the shape language shared by cards, panels, and interactive surfaces.', 'elementor-extension-kit'),
            'border' => __('Set the shared structural border weight.', 'elementor-extension-kit'),
            'shadow' => __('Choose a consistent default elevation without writing custom CSS.', 'elementor-extension-kit'),
            'surface' => __('Tune subtle background contrast used by muted surfaces.', 'elementor-extension-kit'),
            'motion' => __('Set one site-wide motion policy. Reduced-motion preferences always take precedence.', 'elementor-extension-kit'),
            default => '',
        };
    }
}
