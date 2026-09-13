<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use ElementorExtensionKit\Core\Settings\SettingsStore;

final class ElementTemplateResolver
{
    public static function resolve(
        string $elementId,
        mixed $localTemplate,
        bool $hasLocalOverride,
        ?string $mode = null
    ): ?array {
        if ($hasLocalOverride && is_string($localTemplate)) {
            $local = self::valid($elementId, $localTemplate, $mode);
            if ($local !== null) {
                return $local;
            }
        }

        $settings = SettingsStore::get();
        $global = $settings['templates'][$elementId] ?? null;
        if (is_string($global)) {
            $resolved = self::valid($elementId, $global, $mode);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    private static function valid(string $elementId, string $templateId, ?string $mode): ?array
    {
        if ($templateId === '' || in_array($templateId, ['default', 'inherit'], true)) {
            return null;
        }

        $template = ElementTemplateRegistry::get($elementId, $templateId);
        if ($template === null) {
            return null;
        }

        if ($mode !== null && $mode !== '') {
            $modes = is_array($template['modes'] ?? null) ? $template['modes'] : [];
            if ($modes !== [] && ! in_array($mode, $modes, true)) {
                return null;
            }
        }

        return $template;
    }
}
