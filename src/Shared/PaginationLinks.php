<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Shared;

final class PaginationLinks
{
    /**
     * @return list<string>
     */
    public static function build(int $current, int $total, string $previous, string $next, int $midSize = 1): array
    {
        $total = max(1, $total);
        if ($total <= 1) {
            return [];
        }

        $links = paginate_links([
            'base' => str_replace(999999999, '%#%', esc_url_raw(get_pagenum_link(999999999))),
            'current' => max(1, $current),
            'total' => $total,
            'mid_size' => max(0, min(5, $midSize)),
            'prev_text' => $previous,
            'next_text' => $next,
            'type' => 'array',
        ]);

        if (! is_array($links)) {
            return [];
        }

        return array_values(array_map('strval', $links));
    }
}
