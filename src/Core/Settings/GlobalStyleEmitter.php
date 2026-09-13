<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class GlobalStyleEmitter
{
    private static ?string $cache = null;

    public static function css(): string
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $settings = SettingsStore::get();
        $design = isset($settings['design']) && is_array($settings['design']) ? $settings['design'] : [];
        $vars = [];

        self::numberVar($vars, $design, 'spacing', 'component_gap', '--eek-component-gap', 'rem');
        self::numberVar($vars, $design, 'spacing', 'component_padding', '--eek-component-padding', 'rem');
        self::numberVar($vars, $design, 'spacing', 'section', '--eek-space-section', 'rem');
        self::numberVar($vars, $design, 'radius', 'sm', '--eek-radius-sm', 'rem');
        self::numberVar($vars, $design, 'radius', 'md', '--eek-radius-md', 'rem');
        self::numberVar($vars, $design, 'radius', 'lg', '--eek-radius-lg', 'rem');
        self::numberVar($vars, $design, 'border', 'width', '--eek-border-width', 'px');
        self::numberVar($vars, $design, 'motion', 'duration', '--eek-motion-duration', 'ms');

        $shadow = self::stringValue($design, 'shadow', 'level');
        if (in_array($shadow, ['none', 'sm', 'md', 'lg'], true)) {
            $vars['--eek-card-shadow'] = $shadow === 'none' ? 'none' : 'var(--eek-shadow-' . $shadow . ')';
        }

        $surface = self::numberValue($design, 'surface', 'muted_strength');
        if ($surface !== null) {
            $surface = max(0, min(16, $surface));
            $vars['--eek-color-surface-muted'] = 'color-mix(in srgb, CanvasText ' . self::formatNumber($surface) . '%, Canvas)';
        }

        $easing = self::stringValue($design, 'motion', 'easing');
        $easingMap = [
            'standard' => 'cubic-bezier(.2,0,0,1)',
            'emphasized' => 'cubic-bezier(.2,0,0,1.2)',
            'linear' => 'linear',
        ];
        if (isset($easingMap[$easing])) {
            $vars['--eek-motion-easing'] = $easingMap[$easing];
        }

        $mode = self::stringValue($design, 'motion', 'mode');
        if (in_array($mode, ['standard', 'subtle', 'none'], true)) {
            $vars['--eek-motion-scale'] = match ($mode) {
                'none' => '0',
                'subtle' => '.5',
                default => '1',
            };
        }

        if ($vars === []) {
            return self::$cache = '';
        }

        $parts = [];
        foreach ($vars as $name => $value) {
            $parts[] = $name . ':' . $value;
        }

        return self::$cache = ':root{' . implode(';', $parts) . '}';
    }

    /**
     * @param array<string, string> $vars
     * @param array<string, mixed> $design
     */
    private static function numberVar(array &$vars, array $design, string $group, string $key, string $cssVar, string $unit): void
    {
        $value = self::numberValue($design, $group, $key);
        if ($value === null) {
            return;
        }
        $vars[$cssVar] = self::formatNumber($value) . $unit;
    }

    /** @param array<string, mixed> $design */
    private static function numberValue(array $design, string $group, string $key): ?float
    {
        $value = isset($design[$group]) && is_array($design[$group]) ? ($design[$group][$key] ?? null) : null;

        return is_numeric($value) ? (float) $value : null;
    }

    /** @param array<string, mixed> $design */
    private static function stringValue(array $design, string $group, string $key): string
    {
        $value = isset($design[$group]) && is_array($design[$group]) ? ($design[$group][$key] ?? '') : '';

        return is_string($value) ? sanitize_key($value) : '';
    }

    private static function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }
}
