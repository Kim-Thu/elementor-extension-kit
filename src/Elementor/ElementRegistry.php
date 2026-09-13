<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use Elementor\Widget_Base;
use Elementor\Widgets_Manager;
use ElementorExtensionKit\Core\Plugin;

final class ElementRegistry
{
    public static function registerWidgets(Widgets_Manager $widgetsManager): void
    {
        foreach (ElementManifestRepository::all() as $manifest) {
            $class = $manifest['class'];

            if (
                ! is_string($class)
                || ! str_starts_with($class, 'ElementorExtensionKit\\Elements\\')
                || ! class_exists($class)
                || ! is_subclass_of($class, Widget_Base::class)
            ) {
                continue;
            }

            $widgetsManager->register(new $class());
        }
    }

    public static function registerAssets(): void
    {
        $seenHandles = [];

        foreach (ElementManifestRepository::all() as $manifest) {
            $handle = $manifest['handle'];
            if (! is_string($handle) || isset($seenHandles[$handle])) {
                continue;
            }

            $seenHandles[$handle] = true;
            $manifestPath = isset($manifest['_path']) && is_string($manifest['_path']) ? $manifest['_path'] : '';
            if ($manifestPath === '') {
                continue;
            }

            $directory = dirname($manifestPath);
            $relativeDirectory = ltrim(str_replace(dirname(__DIR__, 2), '', $directory), '/\\');
            $style = self::resolveLocalAsset($directory, $manifest['style'] ?? null);
            $script = self::resolveLocalAsset($directory, $manifest['script'] ?? null);

            if ($style !== null) {
                wp_register_style(
                    $handle,
                    Plugin::pluginUrl($relativeDirectory . '/' . $style),
                    [],
                    Plugin::VERSION
                );
            }

            if ($script !== null) {
                wp_register_script(
                    $handle,
                    Plugin::pluginUrl($relativeDirectory . '/' . $script),
                    ['elementor-frontend'],
                    Plugin::VERSION,
                    true
                );
            }
        }
    }

    private static function resolveLocalAsset(string $moduleDirectory, mixed $path): ?string
    {
        if (! is_string($path) || $path === '' || str_contains($path, "\0")) {
            return null;
        }

        $relative = str_replace('\\', '/', trim($path));

        if (
            $relative === ''
            || str_starts_with($relative, '/')
            || preg_match('/^[A-Za-z]:\//', $relative) === 1
            || in_array('..', explode('/', $relative), true)
        ) {
            return null;
        }

        $moduleRoot = realpath($moduleDirectory);
        $assetPath = realpath($moduleDirectory . DIRECTORY_SEPARATOR . $relative);

        if (
            $moduleRoot === false
            || $assetPath === false
            || ! is_file($assetPath)
            || ! str_starts_with($assetPath, $moduleRoot . DIRECTORY_SEPARATOR)
        ) {
            return null;
        }

        return $relative;
    }
}
