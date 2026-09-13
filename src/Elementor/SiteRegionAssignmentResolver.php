<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

use ElementorExtensionKit\Core\Settings\SettingsStore;

final class SiteRegionAssignmentResolver
{
    public static function resolve(string $region): ?int
    {
        if (! in_array($region, ['header', 'footer'], true)) {
            return null;
        }

        $settings = SettingsStore::get();
        $raw = $settings['regions'][$region] ?? null;
        $postId = is_numeric($raw) ? (int) $raw : 0;

        if (! ElementorRegionTemplateRepository::isValid($region, $postId)) {
            return null;
        }

        return $postId;
    }
}
