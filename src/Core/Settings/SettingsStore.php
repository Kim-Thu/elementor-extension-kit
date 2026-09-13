<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class SettingsStore
{
    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $stored = get_option(SettingsSchema::OPTION_NAME, []);

        return SettingsSchema::hydrate(is_array($stored) ? $stored : []);
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function update(array $input): bool
    {
        $clean = SettingsSanitizer::sanitize($input);

        return update_option(SettingsSchema::OPTION_NAME, $clean, false);
    }

    /**
     * @param array<string, mixed> $partial
     */
    public static function updatePartial(array $partial): bool
    {
        $merged = array_replace_recursive(self::get(), $partial);

        return self::update($merged);
    }

    public static function reset(): bool
    {
        return delete_option(SettingsSchema::OPTION_NAME);
    }
}
