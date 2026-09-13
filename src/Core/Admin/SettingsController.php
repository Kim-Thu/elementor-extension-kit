<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

use ElementorExtensionKit\Core\Settings\SettingsStore;

final class SettingsController
{
    public static function save(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to update EEK settings.', 'elementor-extension-kit'));
        }

        check_admin_referer('eek_save_global_settings');

        $raw = isset($_POST['eek']) && is_array($_POST['eek'])
            ? wp_unslash($_POST['eek'])
            : [];

        $updated = SettingsStore::update(is_array($raw) ? $raw : []);
        $section = isset($_POST['eek_section']) ? sanitize_key((string) wp_unslash($_POST['eek_section'])) : 'overview';

        $url = add_query_arg(
            [
                'page' => GlobalSettingsPage::PAGE_SLUG,
                'section' => SettingsNavigation::resolve($section),
                'eek_status' => $updated ? 'saved' : 'unchanged',
            ],
            admin_url('admin.php')
        );

        wp_safe_redirect($url);
        exit;
    }
}
