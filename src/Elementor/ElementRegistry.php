<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use Elementor\Widget_Base;
use Elementor\Widgets_Manager;
use ElementorExtensionKit\Core\Plugin;

final class ElementRegistry
{
    /** @var array<int, string>|null */
    private static ?array $manifestPaths = null;

    /** @var array<string, array<string, mixed>> */
    private static array $manifestCache = [];

    /**
     * @return array<int, string>
     */
    private static function manifests(): array
    {
        if (self::$manifestPaths !== null) {
            return self::$manifestPaths;
        }

        self::$manifestPaths = glob(dirname(__DIR__) . '/Elements/*/*/element.json') ?: [];

        return self::$manifestPaths;
    }

    public static function registerWidgets(Widgets_Manager $widgetsManager): void
    {
        foreach (self::manifests() as $manifestPath) {
            $manifest = self::readManifest($manifestPath);

            if (! self::hasRequiredContract($manifest)) {
                continue;
            }

            $class = $manifest['class'];

            if (
                ! str_starts_with($class, 'ElementorExtensionKit\\Elements\\')
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

        foreach (self::manifests() as $manifestPath) {
            $manifest = self::readManifest($manifestPath);

            if (! self::hasRequiredContract($manifest)) {
                continue;
            }

            $handle = $manifest['handle'];

            if (isset($seenHandles[$handle])) {
                continue;
            }

            $seenHandles[$handle] = true;
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

    /**
     * @return array<string, mixed>
     */
    private static function readManifest(string $path): array
    {
        if (isset(self::$manifestCache[$path])) {
            return self::$manifestCache[$path];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return self::$manifestCache[$path] = [];
        }

        $decoded = json_decode($contents, true);

        return self::$manifestCache[$path] = is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $manifest
     */
    private static function hasRequiredContract(array $manifest): bool
    {
        foreach (['id', 'name', 'class', 'handle'] as $field) {
            if (! isset($manifest[$field]) || ! is_string($manifest[$field]) || $manifest[$field] === '') {
                return false;
            }
        }

        return preg_match('/^[a-z][a-z0-9-]*$/', $manifest['id']) === 1
            && preg_match('/^eek-[a-z0-9-]+$/', $manifest['handle']) === 1;
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
