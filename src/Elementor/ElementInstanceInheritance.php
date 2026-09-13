<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class ElementInstanceInheritance
{
    public const INHERIT = 'inherit';
    public const OVERRIDE = 'override';

    /**
     * @param array<string, mixed> $settings
     * @return array{has_local: bool, value: mixed}
     */
    public static function local(array $settings, string $key): array
    {
        $modeKey = $key . '_source';
        $valueKey = $key . '_override';
        $mode = isset($settings[$modeKey]) ? (string) $settings[$modeKey] : self::INHERIT;

        if ($mode !== self::OVERRIDE || ! array_key_exists($valueKey, $settings)) {
            return ['has_local' => false, 'value' => null];
        }

        return ['has_local' => true, 'value' => $settings[$valueKey]];
    }
}
