<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elements\Content\Card;

final class CardLogic
{
    /**
     * @param array<string, mixed> $settings
     * @return array{title:string,description:string,has_title:bool,has_description:bool}
     */
    public static function toViewModel(array $settings): array
    {
        $title = sanitize_text_field((string) ($settings['title'] ?? ''));
        $description = wp_kses_post((string) ($settings['description'] ?? ''));

        return [
            'title' => $title,
            'description' => $description,
            'has_title' => $title !== '',
            'has_description' => $description !== '',
        ];
    }

    private function __construct()
    {
    }
}
