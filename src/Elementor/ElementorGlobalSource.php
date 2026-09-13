<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class ElementorGlobalSource
{
    public static function activeKitId(): ?int
    {
        if (! class_exists('\Elementor\Plugin')) {
            return null;
        }

        $plugin = \Elementor\Plugin::$instance ?? null;
        if ($plugin === null || ! isset($plugin->kits_manager)) {
            return null;
        }

        $id = (int) $plugin->kits_manager->get_active_id();

        return $id > 0 ? $id : null;
    }

    public static function siteSettingsUrl(): string
    {
        $kitId = self::activeKitId();
        if ($kitId === null) {
            return admin_url('admin.php?page=elementor');
        }

        return add_query_arg(
            [
                'post' => $kitId,
                'action' => 'elementor',
            ],
            admin_url('post.php')
        );
    }

    /**
     * @return array<string, string>
     */
    public static function ownership(): array
    {
        return [
            'colors' => __('Elementor Global Colors', 'elementor-extension-kit'),
            'typography' => __('Elementor Global Fonts / Typography', 'elementor-extension-kit'),
            'layout' => __('Elementor Site Layout', 'elementor-extension-kit'),
        ];
    }
}
