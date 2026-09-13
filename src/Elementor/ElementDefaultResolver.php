<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use ElementorExtensionKit\Core\Settings\SettingsStore;

final class ElementDefaultResolver
{
    public static function resolve(
        string $elementId,
        string $key,
        mixed $localValue,
        bool $hasLocalValue,
        mixed $fallback = null
    ): mixed {
        $schemas = ElementManifestRepository::globalDefaults();
        $schema = $schemas[$elementId][$key] ?? null;
        if (! is_array($schema)) {
            return $hasLocalValue ? $localValue : $fallback;
        }

        if ($hasLocalValue && $localValue !== 'inherit' && self::isAllowed($schema, $localValue)) {
            return $localValue;
        }

        $settings = SettingsStore::get();
        $global = isset($settings['elements'][$elementId]) && is_array($settings['elements'][$elementId])
            ? ($settings['elements'][$elementId][$key] ?? null)
            : null;

        if ($global !== null && self::isAllowed($schema, $global)) {
            return $global;
        }

        $default = $schema['default'] ?? null;

        return self::isAllowed($schema, $default) ? $default : $fallback;
    }

    /** @param array<string, mixed> $schema */
    public static function isAllowed(array $schema, mixed $value): bool
    {
        return match ($schema['type'] ?? '') {
            'select' => is_string($value)
                && isset($schema['options'])
                && is_array($schema['options'])
                && array_key_exists($value, $schema['options']),
            'toggle' => in_array($value, ['yes', 'no', true, false, 1, 0, '1', '0'], true),
            'number' => is_numeric($value),
            default => false,
        };
    }
}
