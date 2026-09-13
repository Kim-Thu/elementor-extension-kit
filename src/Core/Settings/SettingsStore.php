<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class SettingsStore
{
    public static function get(): array
    {
        $stored = get_option(SettingsSchema::OPTION_NAME, []);
        return SettingsSchema::hydrate(is_array($stored) ? $stored : []);
    }

    public static function update(array $input): bool
    {
        $clean = SettingsSanitizer::sanitize($input);
        return update_option(SettingsSchema::OPTION_NAME, $clean, false);
    }

    public static function updatePartial(array $partial): bool
    {
        $merged = array_replace_recursive(self::get(), $partial);
        return self::update($merged);
    }

    public static function resetTemplate(string $elementId): bool
    {
        $settings = self::get();
        if (! isset($settings['templates']) || ! is_array($settings['templates']) || ! array_key_exists($elementId, $settings['templates'])) {
            return false;
        }
        unset($settings['templates'][$elementId]);
        return self::update($settings);
    }

    public static function reset(): bool
    {
        return delete_option(SettingsSchema::OPTION_NAME);
    }
}
