<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class SettingsSchema
{
    public const OPTION_NAME = 'eek_global_settings';
    public const VERSION = 1;

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'version' => self::VERSION,
            'design' => [
                'spacing' => [],
                'radius' => [],
                'border' => [],
                'shadow' => [],
                'surface' => [],
                'motion' => [],
            ],
            'elements' => [],
            'templates' => [],
            'regions' => [
                'header' => null,
                'footer' => null,
            ],
        ];
    }

    /**
     * Merge persisted settings with the current schema without copying
     * resolved Elementor values into EEK-owned storage.
     *
     * @param array<string, mixed> $stored
     * @return array<string, mixed>
     */
    public static function hydrate(array $stored): array
    {
        $defaults = self::defaults();

        return [
            'version' => self::VERSION,
            'design' => self::section($stored, 'design', $defaults['design']),
            'elements' => self::map($stored, 'elements'),
            'templates' => self::map($stored, 'templates'),
            'regions' => self::section($stored, 'regions', $defaults['regions']),
        ];
    }

    /**
     * Remove values that explicitly mean "inherit" so future global changes
     * continue to cascade into existing element instances.
     *
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public static function compact(array $settings): array
    {
        $settings = self::hydrate($settings);
        $settings['design'] = self::compactRecursive($settings['design']);
        $settings['elements'] = self::compactRecursive($settings['elements']);
        $settings['templates'] = self::compactRecursive($settings['templates']);
        $settings['regions'] = self::compactRecursive($settings['regions']);

        return $settings;
    }

    /**
     * @param array<string, mixed> $source
     * @param array<string, mixed> $fallback
     * @return array<string, mixed>
     */
    private static function section(array $source, string $key, array $fallback): array
    {
        $value = $source[$key] ?? null;

        return is_array($value) ? array_replace_recursive($fallback, $value) : $fallback;
    }

    /**
     * @param array<string, mixed> $source
     * @return array<string, mixed>
     */
    private static function map(array $source, string $key): array
    {
        return isset($source[$key]) && is_array($source[$key]) ? $source[$key] : [];
    }

    /**
     * @param array<string, mixed> $value
     * @return array<string, mixed>
     */
    private static function compactRecursive(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $item = self::compactRecursive($item);

                if ($item === []) {
                    unset($value[$key]);
                    continue;
                }

                $value[$key] = $item;
                continue;
            }

            if ($item === null || $item === '' || $item === 'inherit' || $item === 'default') {
                unset($value[$key]);
            }
        }

        return $value;
    }
}
