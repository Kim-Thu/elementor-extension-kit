<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Settings\SettingsStore;
use ElementorExtensionKit\Elementor\ElementManifestRepository;
use ElementorExtensionKit\Elementor\ElementTemplateRegistry;

final class TemplateDefaultsPanel
{
    public static function render(): void
    {
        $registry = ElementTemplateRegistry::all();
        if ($registry === []) {
            self::emptyState();
            return;
        }

        $settings = SettingsStore::get();
        $selected = is_array($settings['templates'] ?? null) ? $settings['templates'] : [];
        $names = self::elementNames();

        echo '<section class="eek-settings__section">';
        echo '<div class="eek-settings__section-head"><div>';
        echo '<h3 class="eek-settings__section-title">' . esc_html__('Element presentation defaults', 'elementor-extension-kit') . '</h3>';
        echo '<p class="eek-settings__section-copy">' . esc_html__('Choose once for the whole site. Default / Native leaves the element on its built-in rendering.', 'elementor-extension-kit') . '</p>';
        echo '</div></div>';
        echo '<div class="eek-settings__section-body">';
        echo '<label class="screen-reader-text" for="eek-template-search">' . esc_html__('Search elements', 'elementor-extension-kit') . '</label>';
        echo '<input id="eek-template-search" class="regular-text eek-template-search" type="search" placeholder="' . esc_attr__('Search elements…', 'elementor-extension-kit') . '" data-eek-template-search>';
        echo '</div></section>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="eek_save_global_settings">';
        echo '<input type="hidden" name="eek_section" value="templates">';
        wp_nonce_field('eek_save_global_settings');

        echo '<div data-eek-template-list>';
        foreach ($registry as $elementId => $templates) {
            $label = $names[$elementId] ?? $elementId;
            $value = is_string($selected[$elementId] ?? null) ? $selected[$elementId] : 'default';
            $options = ['default' => __('Default / Native', 'elementor-extension-kit')];
            foreach ($templates as $templateId => $template) {
                $options[$templateId] = (string) ($template['name'] ?? $templateId);
            }

            echo '<section class="eek-settings__section" data-eek-template-row data-search="' . esc_attr(strtolower($label . ' ' . $elementId)) . '">';
            echo '<div class="eek-settings__section-body">';
            SettingsControls::field(
                $label,
                __('All instances that remain on Inherit use this presentation.', 'elementor-extension-kit'),
                static function () use ($elementId, $options, $value): void {
                    $id = 'eek-template-' . sanitize_html_class($elementId);
                    SettingsControls::select('eek[templates][' . $elementId . ']', $id, $options, $value);
                    EffectiveConfiguration::renderState(
                        $value === 'default' ? 'default' : 'eek-global',
                        $value === 'default' ? __('Built-in rendering', 'elementor-extension-kit') : (string) ($options[$value] ?? $value)
                    );
                }
            );
            echo '</div></section>';
        }
        echo '</div>';

        echo '<div class="eek-settings__actions">';
        EffectiveConfiguration::renderDirtyStatus(false, __('Template defaults affect only instances that inherit.', 'elementor-extension-kit'));
        submit_button(__('Save template defaults', 'elementor-extension-kit'), 'primary', 'submit', false);
        echo '</div></form>';
    }

    private static function elementNames(): array
    {
        $names = [];
        foreach (ElementManifestRepository::all() as $manifest) {
            if (is_string($manifest['id'] ?? null) && is_string($manifest['name'] ?? null)) {
                $names[$manifest['id']] = $manifest['name'];
            }
        }
        return $names;
    }

    private static function emptyState(): void
    {
        echo '<section class="eek-settings__section"><div class="eek-empty">';
        echo '<h3>' . esc_html__('No element templates are registered yet.', 'elementor-extension-kit') . '</h3>';
        echo '<p>' . esc_html__('Elements keep their built-in rendering until module-local templates are added.', 'elementor-extension-kit') . '</p>';
        echo '</div></section>';
    }
}
