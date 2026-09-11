<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core;

use Elementor\Elements_Manager;
use ElementorExtensionKit\Elementor\ElementRegistry;

final class Plugin
{
    public const VERSION = '0.1.0';
    public const ELEMENT_CATEGORY = 'eek-elements';

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

        add_action('elementor/elements/categories_registered', [self::class, 'registerElementCategories']);
        add_action('elementor/widgets/register', [ElementRegistry::class, 'registerWidgets']);
        add_action('wp_enqueue_scripts', [ElementRegistry::class, 'registerAssets']);
        add_action('elementor/editor/after_enqueue_styles', [self::class, 'enqueueEditorStyles']);
    }

    public static function registerElementCategories(Elements_Manager $elementsManager): void
    {
        $elementsManager->add_category(
            self::ELEMENT_CATEGORY,
            [
                'title' => esc_html__('Elementor Extension Kit', 'elementor-extension-kit'),
                'icon' => 'eicon-apps',
            ]
        );
    }

    public static function enqueueEditorStyles(): void
    {
        $handle = 'eek-elementor-editor';

        wp_enqueue_style(
            $handle,
            self::pluginUrl('assets/editor/elementor-editor.css'),
            [],
            self::VERSION
        );

        $brandMarkPath = dirname(self::$pluginFile) . '/assets/editor/brand-mark.svg';

        if (! is_readable($brandMarkPath)) {
            return;
        }

        $brandMarkUrl = self::pluginUrl('assets/editor/brand-mark.svg');
        $inlineStyle = sprintf(
            ':root{--eek-editor-brand-mark-image:url("%s");}',
            esc_url_raw($brandMarkUrl)
        );

        wp_add_inline_style($handle, $inlineStyle);
    }

    public static function pluginUrl(string $path = ''): string
    {
        return plugin_dir_url(self::$pluginFile) . ltrim($path, '/');
    }
}
