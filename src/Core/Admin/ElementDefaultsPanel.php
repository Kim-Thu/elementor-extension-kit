<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Settings\SettingsStore;
use ElementorExtensionKit\Elementor\ElementManifestRepository;

final class ElementDefaultsPanel
{
    public static function render(): void
    {
        $schemas = ElementManifestRepository::globalDefaults();
        $settings = SettingsStore::get();
        $stored = isset($settings['elements']) && is_array($settings['elements']) ? $settings['elements'] : [];

        if ($schemas === []) {
            echo '<section class="eek-settings__section"><div class="eek-empty">';
            echo '<h3>' . esc_html__('No global element defaults are exposed yet.', 'elementor-extension-kit') . '</h3>';
            echo '<p>' . esc_html__('Elements opt in through their local manifest; content controls are never promoted automatically.', 'elementor-extension-kit') . '</p>';
            echo '</div></section>';
            return;
        }

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="eek_save_global_settings">';
        echo '<input type="hidden" name="eek_section" value="elements">';
        wp_nonce_field('eek_save_global_settings');

        foreach ($schemas as $elementId => $controls) {
            echo '<section class="eek-settings__section">';
            echo '<div class="eek-settings__section-head"><div>';
            printf('<h3 class="eek-settings__section-title">%s</h3>', esc_html(self::elementName($elementId)));
            echo '<p class="eek-settings__section-copy">' . esc_html__('Applies to every instance still set to Inherit.', 'elementor-extension-kit') . '</p>';
            echo '</div></div><div class="eek-settings__section-body">';

            foreach ($controls as $key => $schema) {
                self::renderControl($elementId, $key, $schema, $stored);
            }

            echo '</div></section>';
        }

        echo '<div class="eek-settings__actions">';
        EffectiveConfiguration::renderDirtyStatus(false, __('Element defaults remain inherited until you save an override.', 'elementor-extension-kit'));
        submit_button(__('Save element defaults', 'elementor-extension-kit'), 'primary', 'submit', false);
        echo '</div></form>';
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<string, mixed> $stored
     */
    private static function renderControl(string $elementId, string $key, array $schema, array $stored): void
    {
        $id = 'eek-element-' . sanitize_html_class($elementId . '-' . $key);
        $name = 'eek[elements][' . $elementId . '][' . $key . ']';
        $value = isset($stored[$elementId]) && is_array($stored[$elementId]) ? ($stored[$elementId][$key] ?? null) : null;
        $default = $schema['default'] ?? null;

        SettingsControls::field(
            (string) ($schema['label'] ?? $key),
            sprintf(__('Built-in default: %s. Choose Inherit to keep the manifest default unless changed globally later.', 'elementor-extension-kit'), (string) $default),
            static function () use ($schema, $name, $id, $value): void {
                if (($schema['type'] ?? '') === 'select') {
                    $options = ['inherit' => __('Inherit', 'elementor-extension-kit')];
                    foreach ((array) ($schema['options'] ?? []) as $optionValue => $optionLabel) {
                        $options[(string) $optionValue] = (string) $optionLabel;
                    }
                    SettingsControls::select($name, $id, $options, $value === null ? 'inherit' : (string) $value);
                }
                EffectiveConfiguration::renderState($value === null ? 'default' : 'eek-global', $value === null ? __('Manifest default', 'elementor-extension-kit') : (string) $value);
            },
            $id
        );
    }

    private static function elementName(string $elementId): string
    {
        foreach (ElementManifestRepository::all() as $manifest) {
            if (($manifest['id'] ?? '') === $elementId) {
                return (string) ($manifest['name'] ?? $elementId);
            }
        }

        return $elementId;
    }
}
