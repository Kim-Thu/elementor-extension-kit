<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Core\Settings\ConfigurationDiagnostics;
use ElementorExtensionKit\Elementor\ElementorGlobalSource;

final class GlobalSettingsPage
{
    public const PAGE_SLUG = 'eek-global-settings';

    public static function registerMenu(): void
    {
        add_menu_page(
            __('Elementor Extension Kit', 'elementor-extension-kit'),
            __('EEK Settings', 'elementor-extension-kit'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render'],
            'dashicons-admin-customizer',
            58
        );
    }

    public static function enqueueAssets(string $hook): void
    {
        if ($hook !== 'toplevel_page_' . self::PAGE_SLUG) {
            return;
        }

        wp_enqueue_style(
            'eek-global-settings',
            Plugin::pluginUrl('assets/admin/global-settings.css'),
            [],
            Plugin::VERSION
        );
    }

    public static function render(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to manage EEK settings.', 'elementor-extension-kit'));
        }

        $current = SettingsNavigation::resolve(isset($_GET['section']) ? (string) wp_unslash($_GET['section']) : null);
        $sections = SettingsNavigation::sections();
        $section = $sections[$current];
        $status = isset($_GET['eek_status']) ? sanitize_key((string) wp_unslash($_GET['eek_status'])) : '';

        echo '<div class="wrap eek-settings">';
        echo '<header class="eek-settings__header">';
        echo '<div>';
        echo '<p class="eek-settings__eyebrow">' . esc_html__('Elementor Extension Kit', 'elementor-extension-kit') . '</p>';
        echo '<h1 class="eek-settings__title">' . esc_html__('Global Settings', 'elementor-extension-kit') . '</h1>';
        echo '<p class="eek-settings__lede">' . esc_html__('Configure once, inherit across the site. Keep local overrides only for deliberate exceptions.', 'elementor-extension-kit') . '</p>';
        echo '</div>';
        echo '</header>';

        self::renderStatus($status);

        echo '<div class="eek-settings__layout">';
        SettingsNavigation::render($current, self::PAGE_SLUG);
        echo '<main class="eek-settings__content">';
        self::renderSectionHeader($section['label'], $section['description']);
        self::renderSection($current);
        echo '</main></div></div>';
    }

    private static function renderStatus(string $status): void
    {
        $message = match ($status) {
            'saved' => __('Global settings saved.', 'elementor-extension-kit'),
            'unchanged' => __('No settings changed.', 'elementor-extension-kit'),
            'reset' => __('EEK global settings reset to inheritance.', 'elementor-extension-kit'),
            default => '',
        };

        if ($message !== '') {
            printf('<p class="eek-settings__status" data-state="success" role="status">%s</p>', esc_html($message));
        }
    }

    private static function renderSectionHeader(string $title, string $description): void
    {
        echo '<section class="eek-settings__section">';
        echo '<div class="eek-settings__section-head"><div>';
        printf('<h2 class="eek-settings__section-title">%s</h2>', esc_html($title));
        printf('<p class="eek-settings__section-copy">%s</p>', esc_html($description));
        echo '</div></div></section>';
    }

    private static function renderSection(string $section): void
    {
        match ($section) {
            'design-system' => self::renderDesignSystem(),
            'elements' => self::renderElements(),
            'templates' => self::renderTemplates(),
            'regions' => self::renderRegions(),
            'diagnostics' => self::renderDiagnostics(),
            default => self::renderOverview(),
        };
    }

    private static function renderOverview(): void
    {
        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(
            __('Design source', 'elementor-extension-kit'),
            __('Colors and typography stay owned by Elementor; EEK adds only missing semantic controls.', 'elementor-extension-kit'),
            static function (): void {
                SettingsControls::badge(__('Elementor native globals', 'elementor-extension-kit'), 'native');
                echo ' ';
                SettingsControls::badge(__('EEK semantic gaps', 'elementor-extension-kit'));
                EffectiveConfiguration::renderPreview([
                    __('Colors & type', 'elementor-extension-kit') => __('Elementor', 'elementor-extension-kit'),
                    __('Spacing & shape', 'elementor-extension-kit') => __('EEK Global', 'elementor-extension-kit'),
                    __('Element presentation', 'elementor-extension-kit') => __('Inherited', 'elementor-extension-kit'),
                ]);
            }
        );
        SettingsControls::field(
            __('Workflow', 'elementor-extension-kit'),
            __('Global first. Local override only when a single instance must differ.', 'elementor-extension-kit'),
            static function (): void {
                echo '<strong>' . esc_html__('Global → Element default → Template/Layout → Local override', 'elementor-extension-kit') . '</strong>';
            }
        );
        echo '</div></section>';
    }

    private static function renderDesignSystem(): void
    {
        $ownership = ElementorGlobalSource::ownership();

        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(
            __('Global Colors & Typography', 'elementor-extension-kit'),
            __('Use the active Elementor Kit as the source of truth. EEK never creates a second color or font store.', 'elementor-extension-kit'),
            static function () use ($ownership): void {
                printf(
                    '<a class="button button-secondary" href="%s">%s</a>',
                    esc_url(ElementorGlobalSource::siteSettingsUrl()),
                    esc_html__('Open Elementor Site Settings', 'elementor-extension-kit')
                );
                EffectiveConfiguration::renderState('elementor', implode(' · ', array_values($ownership)));
            }
        );
        SettingsControls::field(
            __('EEK semantic controls', 'elementor-extension-kit'),
            __('Spacing, radius, border, shadow, surfaces, and motion live here so one change cascades through inheriting EEK elements.', 'elementor-extension-kit'),
            static function (): void {
                SettingsControls::badge(__('Configured in this section', 'elementor-extension-kit'));
            }
        );
        echo '</div></section>';
    }

    private static function renderElements(): void
    {
        self::renderEmpty(
            __('Element defaults will appear here automatically.', 'elementor-extension-kit'),
            __('Only presentation controls explicitly exposed by each element are eligible. Content and query data stay local.', 'elementor-extension-kit')
        );
    }

    private static function renderTemplates(): void
    {
        self::renderEmpty(
            __('Template and layout defaults will appear here automatically.', 'elementor-extension-kit'),
            __('Default keeps the element’s built-in/native rendering. Registered variants remain templates of the same element.', 'elementor-extension-kit')
        );
    }

    private static function renderRegions(): void
    {
        self::renderEmpty(
            __('Header and Footer assignments are not configured yet.', 'elementor-extension-kit'),
            __('Leaving a region on Default preserves Elementor/theme behavior.', 'elementor-extension-kit')
        );
    }

    private static function renderDiagnostics(): void
    {
        $diagnostics = ConfigurationDiagnostics::inspect();

        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(
            __('Settings schema', 'elementor-extension-kit'),
            __('Versioned contract used by all Phase 5 settings.', 'elementor-extension-kit'),
            static function () use ($diagnostics): void {
                $label = $diagnostics['schema_current']
                    ? __('Current', 'elementor-extension-kit')
                    : __('Needs migration', 'elementor-extension-kit');
                SettingsControls::badge($label, $diagnostics['schema_current'] ? 'native' : 'warning');
                EffectiveConfiguration::renderPreview([
                    __('Element overrides', 'elementor-extension-kit') => (string) $diagnostics['element_overrides'],
                    __('Template assignments', 'elementor-extension-kit') => (string) $diagnostics['template_assignments'],
                    __('Site regions', 'elementor-extension-kit') => (string) $diagnostics['region_assignments'],
                ]);
            }
        );
        SettingsControls::field(
            __('Reset to inheritance', 'elementor-extension-kit'),
            __('Remove EEK-owned global overrides and return to Elementor/native defaults. Content is not deleted.', 'elementor-extension-kit'),
            static function () use ($diagnostics): void {
                if (! $diagnostics['has_custom_settings']) {
                    SettingsControls::badge(__('Already inheriting', 'elementor-extension-kit'), 'native');
                    return;
                }
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="eek_reset_global_settings">';
                wp_nonce_field('eek_reset_global_settings');
                submit_button(__('Reset EEK globals', 'elementor-extension-kit'), 'secondary', 'submit', false);
                echo '</form>';
            }
        );
        echo '</div></section>';
    }

    private static function renderEmpty(string $title, string $description): void
    {
        echo '<section class="eek-settings__section"><div class="eek-empty">';
        printf('<h3>%s</h3>', esc_html($title));
        printf('<p>%s</p>', esc_html($description));
        echo '</div></section>';
    }
}
