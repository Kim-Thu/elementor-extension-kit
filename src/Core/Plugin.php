<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core;

use ElementorExtensionKit\Elementor\ElementRegistry;

final class Plugin
{
    public const VERSION = '0.1.0';

    private static string $pluginFile;

    public static function boot(string $pluginFile): void
    {
        self::$pluginFile = $pluginFile;

        add_action('plugins_loaded', [self::class, 'init']);
    }

    public static function init(): void
    {
        if (! did_action('elementor/loaded')) {
            return;
        }

        add_action('elementor/widgets/register', [ElementRegistry::class, 'registerWidgets']);
        add_action('wp_enqueue_scripts', [ElementRegistry::class, 'registerAssets']);
    }

    public static function pluginUrl(string $path = ''): string
    {
        return plugin_dir_url(self::$pluginFile) . ltrim($path, '/');
    }
}
