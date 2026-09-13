<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class ConfigurationDiagnostics
{
    /**
     * @param array<string, mixed>|null $settings
     * @return array<string, mixed>
     */
    public static function inspect(?array $settings = null): array
    {
        $settings = $settings ?? SettingsStore::get();
        $storedVersion = isset($settings['version']) ? (int) $settings['version'] : 0;
        $elements = isset($settings['elements']) && is_array($settings['elements']) ? $settings['elements'] : [];
        $templates = isset($settings['templates']) && is_array($settings['templates']) ? $settings['templates'] : [];
        $regions = isset($settings['regions']) && is_array($settings['regions']) ? $settings['regions'] : [];

        return [
            'schema_current' => $storedVersion === SettingsSchema::VERSION,
            'schema_version' => $storedVersion,
            'element_overrides' => count($elements),
            'template_assignments' => count($templates),
            'region_assignments' => count(array_filter($regions, static fn (mixed $value): bool => is_string($value) && $value !== '')),
            'has_custom_settings' => self::hasCustomSettings($settings),
        ];
    }

    /**
     * @param array<string, mixed> $settings
     */
    private static function hasCustomSettings(array $settings): bool
    {
        $compacted = SettingsSchema::compact($settings);
        unset($compacted['version']);

        return $compacted !== [];
    }
}
