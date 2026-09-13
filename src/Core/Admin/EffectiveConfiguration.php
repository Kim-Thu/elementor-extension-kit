<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

final class EffectiveConfiguration
{
    public static function sourceLabel(string $source): string
    {
        return match ($source) {
            'elementor' => __('Elementor', 'elementor-extension-kit'),
            'eek-global' => __('EEK Global', 'elementor-extension-kit'),
            'element-default' => __('Element Default', 'elementor-extension-kit'),
            'template' => __('Template', 'elementor-extension-kit'),
            'local' => __('Local Override', 'elementor-extension-kit'),
            default => __('Default', 'elementor-extension-kit'),
        };
    }

    public static function renderState(string $source, string $effectiveValue = ''): void
    {
        $variant = match ($source) {
            'elementor' => 'native',
            'local' => 'override',
            default => '',
        };

        echo '<div class="eek-field__meta">';
        SettingsControls::badge(self::sourceLabel($source), $variant);

        if ($effectiveValue !== '') {
            printf(
                '<span>%s</span>',
                esc_html(sprintf(__('Effective: %s', 'elementor-extension-kit'), $effectiveValue))
            );
        }

        echo '</div>';
    }

    /**
     * @param array<string, string> $items
     */
    public static function renderPreview(array $items): void
    {
        if ($items === []) {
            return;
        }

        echo '<div class="eek-preview" aria-label="' . esc_attr__('Effective value preview', 'elementor-extension-kit') . '">';
        foreach ($items as $label => $value) {
            echo '<div class="eek-preview__item">';
            printf('<span class="eek-preview__label">%s</span>', esc_html($label));
            printf('<strong>%s</strong>', esc_html($value));
            echo '</div>';
        }
        echo '</div>';
    }

    public static function renderDirtyStatus(bool $dirty, ?string $message = null): void
    {
        $state = $dirty ? 'warning' : 'success';
        $text = $message ?? ($dirty
            ? __('Unsaved global changes', 'elementor-extension-kit')
            : __('All global settings are saved', 'elementor-extension-kit'));

        printf(
            '<span class="eek-settings__status" data-state="%1$s" aria-live="polite">%2$s</span>',
            esc_attr($state),
            esc_html($text)
        );
    }
}
