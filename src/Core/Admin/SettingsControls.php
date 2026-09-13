<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Admin;

final class SettingsControls
{
    /**
     * @param callable():void $renderControl
     */
    public static function field(string $label, string $description, callable $renderControl, string $id = ''): void
    {
        echo '<div class="eek-field">';
        echo '<div class="eek-field__label">';
        if ($id !== '') {
            printf('<label for="%s">%s</label>', esc_attr($id), esc_html($label));
        } else {
            echo esc_html($label);
        }
        if ($description !== '') {
            printf('<span class="eek-field__description">%s</span>', esc_html($description));
        }
        echo '</div><div class="eek-field__control">';
        $renderControl();
        echo '</div></div>';
    }

    /**
     * @param array<string, string> $options
     */
    public static function select(string $name, string $id, array $options, ?string $selected = null): void
    {
        printf('<select id="%1$s" name="%2$s">', esc_attr($id), esc_attr($name));
        foreach ($options as $value => $label) {
            printf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr($value),
                selected((string) $selected, (string) $value, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public static function number(string $name, string $id, ?float $value, float $min, float $max, float $step = 1): void
    {
        printf(
            '<input class="eek-control" type="number" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s">',
            esc_attr($id),
            esc_attr($name),
            $value === null ? '' : esc_attr((string) $value),
            esc_attr((string) $min),
            esc_attr((string) $max),
            esc_attr((string) $step)
        );
    }

    /**
     * @param array<string, string> $choices
     */
    public static function segmented(string $name, array $choices, string $selected): void
    {
        echo '<div class="eek-segmented">';
        foreach ($choices as $value => $label) {
            $id = sanitize_html_class($name . '-' . $value);
            printf(
                '<label for="%1$s"><input type="radio" id="%1$s" name="%2$s" value="%3$s"%4$s><span>%5$s</span></label>',
                esc_attr($id),
                esc_attr($name),
                esc_attr($value),
                checked($selected, $value, false),
                esc_html($label)
            );
        }
        echo '</div>';
    }

    public static function badge(string $label, string $variant = ''): void
    {
        $class = 'eek-badge';
        if ($variant !== '') {
            $class .= ' eek-badge--' . sanitize_html_class($variant);
        }
        printf('<span class="%1$s">%2$s</span>', esc_attr($class), esc_html($label));
    }

    public static function meta(string $source, string $effective = ''): void
    {
        echo '<div class="eek-field__meta">';
        self::badge($source, $source === 'Elementor' ? 'native' : '');
        if ($effective !== '') {
            printf('<span>%s</span>', esc_html($effective));
        }
        echo '</div>';
    }

    public static function notice(string $message, string $variant = 'warning'): void
    {
        printf(
            '<div class="eek-notice eek-notice--%1$s" role="status">%2$s</div>',
            esc_attr(sanitize_html_class($variant)),
            esc_html($message)
        );
    }
}
