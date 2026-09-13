<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class SiteRegionLocationBridge
{
    public static function filterTemplateId(int $themeTemplateId, string $location): int
    {
        if (! in_array($location, ['header', 'footer'], true)) {
            return $themeTemplateId;
        }

        $assigned = SiteRegionAssignmentResolver::resolve($location);

        return $assigned ?? $themeTemplateId;
    }
}
