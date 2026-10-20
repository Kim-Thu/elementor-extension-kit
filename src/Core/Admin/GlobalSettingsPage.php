<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Plugin;
use ElementorExtensionKit\Core\Settings\ConfigurationDiagnostics;

final class GlobalSettingsPage
{
    public const PAGE_SLUG = 'eek-global-settings';

    public static function registerMenu(): void
    {
        add_menu_page(__('Elementor Extension Kit', 'elementor-extension-kit'), __('EEK Settings', 'elementor-extension-kit'), 'manage_options', self::PAGE_SLUG, [self::class, 'render'], 'dashicons-admin-customizer', 58);
        add_action('wp_ajax_eek_settings_section', [self::class, 'ajaxSection']);
    }

    public static function enqueueAssets(string $hook): void
    {
        if ($hook !== 'toplevel_page_' . self::PAGE_SLUG) { return; }
        wp_enqueue_style('eek-global-settings', Plugin::pluginUrl('assets/admin/global-settings.css'), [], Plugin::VERSION);
        wp_enqueue_script('eek-global-settings', Plugin::pluginUrl('assets/admin/global-settings.js'), [], Plugin::VERSION, true);
        wp_add_inline_script('eek-global-settings', 'window.eekSettingsNavigation = ' . wp_json_encode([
            'endpoint' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('eek_settings_section'),
        ]) . ';', 'before');
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
        echo '<div class="wrap eek-settings"><header class="eek-settings__header"><div>';
        echo '<p class="eek-settings__eyebrow">' . esc_html__('Elementor Extension Kit', 'elementor-extension-kit') . '</p>';
        echo '<h1 class="eek-settings__title">' . esc_html__('Global Settings', 'elementor-extension-kit') . '</h1>';
        echo '<p class="eek-settings__lede">' . esc_html__('Configure once, inherit across the site. Keep local overrides only for deliberate exceptions.', 'elementor-extension-kit') . '</p>';
        echo '</div></header>';
        self::renderStatus($status);
        echo '<div class="eek-settings__layout">';
        SettingsNavigation::render($current, self::PAGE_SLUG);
        echo '<main class="eek-settings__content">';
        self::renderSectionHeader($section['label'], $section['description']);
        self::renderSection($current);
        echo '</main></div></div>';
    }

    /**
     * Render only the selected section for same-page navigation.
     * Full admin page links remain the progressive-enhancement fallback.
     */
    public static function ajaxSection(): void
    {
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Access denied.', 'elementor-extension-kit')], 403);
        }
        check_ajax_referer('eek_settings_section', 'nonce');

        $requested = isset($_POST['section']) && is_string($_POST['section'])
            ? sanitize_key(wp_unslash($_POST['section']))
            : '';
        $sections = SettingsNavigation::sections();
        if (! isset($sections[$requested])) {
            wp_send_json_error(['message' => __('Invalid settings section.', 'elementor-extension-kit')], 400);
        }

        ob_start();
        self::renderSectionHeader($sections[$requested]['label'], $sections[$requested]['description']);
        self::renderSection($requested);
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    private static function renderStatus(string $status): void
    {
        $message = match ($status) {
            'saved' => __('Global settings saved.', 'elementor-extension-kit'),
            'unchanged' => __('No settings changed.', 'elementor-extension-kit'),
            'reset' => __('Settings reset to inheritance.', 'elementor-extension-kit'),
            default => '',
        };
        if ($message !== '') { printf('<p class="eek-settings__status" data-state="success" role="status">%s</p>', esc_html($message)); }
    }

    private static function renderSectionHeader(string $title, string $description): void
    {
        echo '<section class="eek-settings__section"><div class="eek-settings__section-head"><div>';
        printf('<h2 class="eek-settings__section-title">%s</h2>', esc_html($title));
        printf('<p class="eek-settings__section-copy">%s</p>', esc_html($description));
        echo '</div></div></section>';
    }

    private static function renderSection(string $section): void
    {
        match ($section) {
            'design-system' => DesignSystemPanel::render(),
            'elements' => ElementDefaultsPanel::render(),
            'templates' => TemplateDefaultsPanel::render(),
            'regions' => SiteRegionsPanel::render(),
            'diagnostics' => self::renderDiagnostics(),
            default => self::renderOverview(),
        };
    }

    private static function renderOverview(): void
    {
        $diagnostics = ConfigurationDiagnostics::inspect();
        $sections = SettingsNavigation::sections();
        $routes = [
            'design-system' => __('Configure site-wide spacing, shape, surfaces and motion.', 'elementor-extension-kit'),
            'elements' => __('Set element defaults while preserving instance overrides.', 'elementor-extension-kit'),
            'templates' => __('Choose layout defaults with native fallback.', 'elementor-extension-kit'),
            'regions' => __('Manage compatible header and footer assignments.', 'elementor-extension-kit'),
        ];

        echo '<section class="eek-overview-intro" aria-labelledby="eek-overview-heading">';
        echo '<div><p class="eek-overview-intro__kicker">' . esc_html__('Configuration workspace', 'elementor-extension-kit') . '</p>';
        echo '<h3 id="eek-overview-heading">' . esc_html__('Make a change once. Keep control everywhere.', 'elementor-extension-kit') . '</h3>';
        echo '<p>' . esc_html__('Elementor owns global colors and typography. EEK controls complementary design tokens, element defaults and presentation. Individual widgets can still override inherited values.', 'elementor-extension-kit') . '</p></div>';
        echo '<a class="eek-overview-intro__action" href="' . esc_url(add_query_arg(['page' => self::PAGE_SLUG, 'section' => 'design-system'], admin_url('admin.php'))) . '">' . esc_html__('Open Design System', 'elementor-extension-kit') . ' &rarr;</a>';
        echo '</section>';

        echo '<section class="eek-overview-group" aria-labelledby="eek-overview-workspaces"><div class="eek-overview-group__heading"><h3 id="eek-overview-workspaces">' . esc_html__('Configuration areas', 'elementor-extension-kit') . '</h3><p>' . esc_html__('Choose what you need to change.', 'elementor-extension-kit') . '</p></div>';
        echo '<div class="eek-overview-links">';
        foreach ($routes as $id => $description) {
            $url = add_query_arg(['page' => self::PAGE_SLUG, 'section' => $id], admin_url('admin.php'));
            echo '<a class="eek-overview-link" href="' . esc_url($url) . '"><span><strong>' . esc_html($sections[$id]['label']) . '</strong><small>' . esc_html($description) . '</small></span><span class="eek-overview-link__arrow" aria-hidden="true">&rarr;</span></a>';
        }
        echo '</div></section>';

        echo '<section class="eek-overview-group" aria-labelledby="eek-overview-health"><div class="eek-overview-group__heading"><h3 id="eek-overview-health">' . esc_html__('Configuration health', 'elementor-extension-kit') . '</h3><p>' . esc_html__('Live summary of saved EEK settings.', 'elementor-extension-kit') . '</p></div>';
        echo '<dl class="eek-overview-stats">';
        $stats = [
            __('Element overrides', 'elementor-extension-kit') => (int) ($diagnostics['element_overrides'] ?? 0),
            __('Template assignments', 'elementor-extension-kit') => (int) ($diagnostics['template_assignments'] ?? 0),
            __('Site regions', 'elementor-extension-kit') => (int) ($diagnostics['region_assignments'] ?? 0),
        ];
        foreach ($stats as $label => $value) {
            echo '<div><dt>' . esc_html($label) . '</dt><dd>' . esc_html((string) $value) . '</dd></div>';
        }
        echo '</dl><a class="eek-overview-diagnostics" href="' . esc_url(add_query_arg(['page' => self::PAGE_SLUG, 'section' => 'diagnostics'], admin_url('admin.php'))) . '">' . esc_html__('View diagnostics and recovery options', 'elementor-extension-kit') . ' &rarr;</a></section>';
    }

    private static function renderDiagnostics(): void
    {
        $diagnostics = ConfigurationDiagnostics::inspect();
        echo '<section class="eek-settings__section"><div class="eek-settings__section-body">';
        SettingsControls::field(__('Settings schema', 'elementor-extension-kit'), __('Versioned contract used by all Phase 5 settings.', 'elementor-extension-kit'), static function () use ($diagnostics): void {
            $label = $diagnostics['schema_current'] ? __('Current', 'elementor-extension-kit') : __('Needs migration', 'elementor-extension-kit');
            SettingsControls::badge($label, $diagnostics['schema_current'] ? 'native' : 'warning');
            EffectiveConfiguration::renderPreview([__('Element overrides', 'elementor-extension-kit') => (string) $diagnostics['element_overrides'], __('Template assignments', 'elementor-extension-kit') => (string) $diagnostics['template_assignments'], __('Site regions', 'elementor-extension-kit') => (string) $diagnostics['region_assignments']]);
        });
        SettingsControls::field(__('Template references', 'elementor-extension-kit'), __('Stale selections automatically fall back to native rendering and can be reset here.', 'elementor-extension-kit'), static function () use ($diagnostics): void {
            $invalid = is_array($diagnostics['invalid_templates'] ?? null) ? $diagnostics['invalid_templates'] : [];
            if ($invalid === []) { SettingsControls::badge(__('Healthy', 'elementor-extension-kit'), 'native'); return; }
            foreach ($invalid as $elementId => $templateId) {
                echo '<div class="eek-notice"><strong>' . esc_html((string) $elementId) . '</strong>: ' . esc_html((string) $templateId);
                echo '<form class="eek-reset-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="eek_reset_template"><input type="hidden" name="element_id" value="' . esc_attr((string) $elementId) . '">';
                wp_nonce_field('eek_reset_template');
                submit_button(__('Reset to native', 'elementor-extension-kit'), 'secondary', 'submit', false);
                echo '</form></div>';
            }
        });
        SettingsControls::field(__('Reset to inheritance', 'elementor-extension-kit'), __('Remove EEK-owned global overrides and return to Elementor/native defaults. Content is not deleted.', 'elementor-extension-kit'), static function () use ($diagnostics): void {
            if (! $diagnostics['has_custom_settings']) { SettingsControls::badge(__('Already inheriting', 'elementor-extension-kit'), 'native'); return; }
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="eek_reset_global_settings">';
            wp_nonce_field('eek_reset_global_settings'); submit_button(__('Reset EEK globals', 'elementor-extension-kit'), 'secondary', 'submit', false); echo '</form>';
        });
        echo '</div></section>';
    }
}
