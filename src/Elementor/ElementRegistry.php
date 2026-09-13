<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use Elementor\Widgets_Manager;
use ElementorExtensionKit\Core\Plugin;

final class ElementRegistry
{
    /**
     * @return array<int, string>
     */
    private static function manifests(): array
    {
        return glob(dirname(__DIR__) . '/Elements/*/*/element.json') ?: [];
    }

    public static function registerWidgets(Widgets_Manager $widgetsManager): void
    {
        foreach (self::manifests() as $manifestPath) {
            $manifest = self::readManifest($manifestPath);
            $class = $manifest['class'] ?? null;

            if (! is_string($class) || ! class_exists($class)) {
                continue;
            }

            $widgetsManager->register(new $class());
        }
    }

    public static function registerAssets(): void
    {
        foreach (self::manifests() as $manifestPath) {
            $manifest = self::readManifest($manifestPath);
            $directory = dirname($manifestPath);
            $relativeDirectory = ltrim(str_replace(dirname(__DIR__, 2), '', $directory), '/\\');
            $handle = $manifest['handle'] ?? null;

            if (! is_string($handle) || $handle === '') {
                continue;
            }

            if (! empty($manifest['style'])) {
                wp_register_style(
                    $handle,
                    Plugin::pluginUrl($relativeDirectory . '/' . ltrim((string) $manifest['style'], '/')),
                    [],
                    Plugin::VERSION
                );
            }

            if (! empty($manifest['script'])) {
                wp_register_script(
                    $handle,
                    Plugin::pluginUrl($relativeDirectory . '/' . ltrim((string) $manifest['script'], '/')),
                    ['elementor-frontend'],
                    Plugin::VERSION,
                    true
                );
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function readManifest(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
