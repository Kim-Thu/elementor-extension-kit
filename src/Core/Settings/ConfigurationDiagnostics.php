<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

use ElementorExtensionKit\Elementor\ElementTemplateRegistry;
use ElementorExtensionKit\Elementor\ElementorRegionTemplateRepository;

final class ConfigurationDiagnostics
{
    public static function inspect(?array $settings = null): array
    {
        $settings = $settings ?? SettingsStore::get();
        $storedVersion = isset($settings['version']) ? (int) $settings['version'] : 0;
        $elements = isset($settings['elements']) && is_array($settings['elements']) ? $settings['elements'] : [];
        $templates = isset($settings['templates']) && is_array($settings['templates']) ? $settings['templates'] : [];
        $regions = isset($settings['regions']) && is_array($settings['regions']) ? $settings['regions'] : [];

        $invalidTemplates = [];
        foreach ($templates as $elementId => $templateId) {
            if (is_string($elementId) && is_string($templateId) && ElementTemplateRegistry::get($elementId, $templateId) === null) {
                $invalidTemplates[$elementId] = $templateId;
            }
        }

        $invalidRegions = [];
        foreach (['header', 'footer'] as $region) {
            $raw = $regions[$region] ?? null;
            if ($raw === null || $raw === '') { continue; }
            $postId = is_numeric($raw) ? (int) $raw : 0;
            if (! ElementorRegionTemplateRepository::isValid($region, $postId)) {
                $invalidRegions[$region] = $raw;
            }
        }

        return [
            'schema_current' => $storedVersion === SettingsSchema::VERSION,
            'schema_version' => $storedVersion,
            'element_overrides' => count($elements),
            'template_assignments' => count($templates),
            'invalid_templates' => $invalidTemplates,
            'invalid_template_count' => count($invalidTemplates),
            'region_assignments' => count(array_filter($regions, static fn (mixed $value): bool => is_string($value) && $value !== '')),
            'invalid_regions' => $invalidRegions,
            'invalid_region_count' => count($invalidRegions),
            'has_custom_settings' => self::hasCustomSettings($settings),
        ];
    }

    private static function hasCustomSettings(array $settings): bool
    {
        $compacted = SettingsSchema::compact($settings);
        unset($compacted['version']);
        return $compacted !== [];
    }
}
