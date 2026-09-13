<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class ElementorRegionTemplateRepository
{
    /** @return array<int, string> */
    public static function forRegion(string $region): array
    {
        if (! in_array($region, ['header', 'footer'], true)) {
            return [];
        }

        $posts = get_posts([
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_key' => '_elementor_template_type',
            'meta_value' => $region,
            'no_found_rows' => true,
        ]);

        $output = [];
        foreach ($posts as $post) {
            if (! $post instanceof \WP_Post) {
                continue;
            }
            $output[(int) $post->ID] = get_the_title($post) ?: sprintf(__('Template #%d', 'elementor-extension-kit'), (int) $post->ID);
        }
        return $output;
    }

    public static function isValid(string $region, int $postId): bool
    {
        if ($postId <= 0 || ! in_array($region, ['header', 'footer'], true)) {
            return false;
        }
        $post = get_post($postId);
        return $post instanceof \WP_Post
            && $post->post_type === 'elementor_library'
            && $post->post_status === 'publish'
            && get_post_meta($postId, '_elementor_template_type', true) === $region;
    }
}
