<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core;

final class SiteContextRefresh
{
    public static function onThemeSwitch(): void
    {
        do_action('eek/site_context_changed', 'theme');
    }

    public static function onKitChange(mixed $oldValue, mixed $newValue): void
    {
        if ((string) $oldValue === (string) $newValue) {
            return;
        }
        do_action('eek/site_context_changed', 'elementor-kit');
    }
}
