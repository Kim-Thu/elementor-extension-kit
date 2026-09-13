<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

final class SettingsNavigation
{
    /**
     * @return array<string, array{label:string,description:string}>
     */
    public static function sections(): array
    {
        return [
            'overview' => [
                'label' => __('Overview', 'elementor-extension-kit'),
                'description' => __('Effective configuration and the fastest paths to common site-wide changes.', 'elementor-extension-kit'),
            ],
            'design-system' => [
                'label' => __('Design System', 'elementor-extension-kit'),
                'description' => __('Elementor-owned colors and typography plus EEK spacing, radius, borders, shadows, surfaces, and motion.', 'elementor-extension-kit'),
            ],
            'elements' => [
                'label' => __('Elements', 'elementor-extension-kit'),
                'description' => __('Site-wide defaults for eligible EEK element presentation controls.', 'elementor-extension-kit'),
            ],
            'templates' => [
                'label' => __('Templates & Layouts', 'elementor-extension-kit'),
                'description' => __('Choose the default presentation for each element while keeping native/default fallback available.', 'elementor-extension-kit'),
            ],
            'regions' => [
                'label' => __('Header & Footer', 'elementor-extension-kit'),
                'description' => __('Assign compatible site-region templates without replacing Elementor or theme builders.', 'elementor-extension-kit'),
            ],
            'diagnostics' => [
                'label' => __('Diagnostics', 'elementor-extension-kit'),
                'description' => __('Inspect effective sources, stale references, and reset settings back to inheritance.', 'elementor-extension-kit'),
            ],
        ];
    }

    public static function resolve(?string $requested): string
    {
        $requested = sanitize_key((string) $requested);

        return array_key_exists($requested, self::sections()) ? $requested : 'overview';
    }

    public static function render(string $current, string $pageSlug): void
    {
        echo '<nav class="eek-settings__nav" aria-label="' . esc_attr__('Global Settings sections', 'elementor-extension-kit') . '">';

        foreach (self::sections() as $id => $section) {
            $url = add_query_arg(
                [
                    'page' => $pageSlug,
                    'section' => $id,
                ],
                admin_url('admin.php')
            );

            printf(
                '<a href="%1$s"%2$s><span>%3$s</span></a>',
                esc_url($url),
                $id === $current ? ' aria-current="page"' : '',
                esc_html($section['label'])
            );
        }

        echo '</nav>';
    }
}
