<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class SettingsSanitizer
{
    private const DESIGN_KEYS = [
        'spacing',
        'radius',
        'border',
        'shadow',
        'surface',
        'motion',
    ];

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function sanitize(array $input): array
    {
        $output = [
            'version' => SettingsSchema::VERSION,
            'design' => [],
            'elements' => [],
            'templates' => [],
            'regions' => [],
        ];

        $design = isset($input['design']) && is_array($input['design']) ? $input['design'] : [];
        foreach (self::DESIGN_KEYS as $key) {
            if (! isset($design[$key]) || ! is_array($design[$key])) {
                continue;
            }
            $output['design'][$key] = self::sanitizeMap($design[$key]);
        }

        if (isset($input['elements']) && is_array($input['elements'])) {
            $output['elements'] = self::sanitizeNestedMap($input['elements']);
        }

        if (isset($input['templates']) && is_array($input['templates'])) {
            $output['templates'] = self::sanitizeTemplateAssignments($input['templates']);
        }

        if (isset($input['regions']) && is_array($input['regions'])) {
            foreach (['header', 'footer'] as $region) {
                $value = $input['regions'][$region] ?? null;
                if ($value === null || $value === '' || $value === 'default' || $value === 'inherit') {
                    $output['regions'][$region] = null;
                    continue;
                }
                $output['regions'][$region] = sanitize_key((string) $value);
            }
        }

        return SettingsSchema::compact($output);
    }

    /**
     * @param array<mixed> $values
     * @return array<string, scalar>
     */
    private static function sanitizeMap(array $values): array
    {
        $output = [];
        foreach ($values as $key => $value) {
            $safeKey = sanitize_key((string) $key);
            if ($safeKey === '' || is_array($value) || is_object($value)) {
                continue;
            }
            $sanitized = self::sanitizeScalar($value);
            if ($sanitized !== null) {
                $output[$safeKey] = $sanitized;
            }
        }
        return $output;
    }

    /**
     * @param array<mixed> $values
     * @return array<string, array<string, scalar>>
     */
    private static function sanitizeNestedMap(array $values): array
    {
        $output = [];
        foreach ($values as $owner => $settings) {
            $safeOwner = sanitize_key((string) $owner);
            if ($safeOwner === '' || ! is_array($settings)) {
                continue;
            }
            $clean = self::sanitizeMap($settings);
            if ($clean !== []) {
                $output[$safeOwner] = $clean;
            }
        }
        return $output;
    }

    /**
     * @param array<mixed> $values
     * @return array<string, string>
     */
    private static function sanitizeTemplateAssignments(array $values): array
    {
        $output = [];
        foreach ($values as $owner => $templateId) {
            $safeOwner = sanitize_key((string) $owner);
            $safeTemplate = sanitize_key((string) $templateId);
            if ($safeOwner === '' || $safeTemplate === '' || in_array($safeTemplate, ['default', 'inherit'], true)) {
                continue;
            }
            $output[$safeOwner] = $safeTemplate;
        }
        return $output;
    }

    /**
     * @return scalar|null
     */
    private static function sanitizeScalar(mixed $value): int|float|string|bool|null
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return max(-10000, min(10000, $value));
        }
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || $value === 'inherit' || $value === 'default') {
            return null;
        }

        return mb_substr(sanitize_text_field($value), 0, 200);
    }
}
