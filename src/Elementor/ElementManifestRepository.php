<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class ElementManifestRepository
{
    /** @var array<int, string>|null */
    private static ?array $paths = null;

    /** @var array<string, array<string, mixed>> */
    private static array $cache = [];

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        $manifests = [];
        foreach (self::paths() as $path) {
            $manifest = self::read($path);
            if (self::isValid($manifest)) {
                $manifest['_path'] = $path;
                $manifests[] = $manifest;
            }
        }

        return $manifests;
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public static function globalDefaults(): array
    {
        $output = [];
        foreach (self::all() as $manifest) {
            $controls = $manifest['global_defaults'] ?? null;
            if (! is_array($controls)) {
                continue;
            }

            $normalized = [];
            foreach ($controls as $key => $definition) {
                $safeKey = sanitize_key((string) $key);
                if ($safeKey === '' || ! is_array($definition)) {
                    continue;
                }
                $type = isset($definition['type']) ? sanitize_key((string) $definition['type']) : '';
                $options = isset($definition['options']) && is_array($definition['options']) ? $definition['options'] : [];
                if (! in_array($type, ['select', 'toggle', 'number'], true)) {
                    continue;
                }
                $normalized[$safeKey] = [
                    'label' => sanitize_text_field((string) ($definition['label'] ?? $safeKey)),
                    'type' => $type,
                    'default' => $definition['default'] ?? null,
                    'options' => self::normalizeOptions($options),
                ];
            }

            if ($normalized !== []) {
                $output[(string) $manifest['id']] = $normalized;
            }
        }

        return $output;
    }

    /**
     * @return array<int, string>
     */
    public static function paths(): array
    {
        if (self::$paths !== null) {
            return self::$paths;
        }

        self::$paths = glob(dirname(__DIR__) . '/Elements/*/*/element.json') ?: [];

        return self::$paths;
    }

    /**
     * @return array<string, mixed>
     */
    public static function read(string $path): array
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path];
        }

        $contents = @file_get_contents($path);
        if ($contents === false) {
            return self::$cache[$path] = [];
        }

        $decoded = json_decode($contents, true);

        return self::$cache[$path] = is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $manifest */
    public static function isValid(array $manifest): bool
    {
        foreach (['id', 'name', 'class', 'handle'] as $field) {
            if (! isset($manifest[$field]) || ! is_string($manifest[$field]) || $manifest[$field] === '') {
                return false;
            }
        }

        return preg_match('/^[a-z][a-z0-9-]*$/', $manifest['id']) === 1
            && preg_match('/^eek-[a-z0-9-]+$/', $manifest['handle']) === 1;
    }

    /**
     * @param array<mixed> $options
     * @return array<string, string>
     */
    private static function normalizeOptions(array $options): array
    {
        $output = [];
        foreach ($options as $value => $label) {
            $safeValue = sanitize_key((string) $value);
            if ($safeValue !== '' && is_scalar($label)) {
                $output[$safeValue] = sanitize_text_field((string) $label);
            }
        }
        return $output;
    }
}
