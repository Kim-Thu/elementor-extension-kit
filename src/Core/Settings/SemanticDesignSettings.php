<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core\Settings;

final class SemanticDesignSettings
{
    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public static function controls(): array
    {
        return [
            'spacing' => [
                'component_gap' => ['label' => __('Component gap', 'elementor-extension-kit'), 'min' => 0, 'max' => 6, 'step' => 0.125, 'default' => 1.0, 'unit' => 'rem'],
                'component_padding' => ['label' => __('Component padding', 'elementor-extension-kit'), 'min' => 0, 'max' => 8, 'step' => 0.125, 'default' => 1.5, 'unit' => 'rem'],
                'section' => ['label' => __('Section spacing', 'elementor-extension-kit'), 'min' => 0, 'max' => 12, 'step' => 0.25, 'default' => 4.0, 'unit' => 'rem'],
            ],
            'radius' => [
                'sm' => ['label' => __('Small radius', 'elementor-extension-kit'), 'min' => 0, 'max' => 4, 'step' => 0.125, 'default' => 0.25, 'unit' => 'rem'],
                'md' => ['label' => __('Medium radius', 'elementor-extension-kit'), 'min' => 0, 'max' => 4, 'step' => 0.125, 'default' => 0.5, 'unit' => 'rem'],
                'lg' => ['label' => __('Large radius', 'elementor-extension-kit'), 'min' => 0, 'max' => 6, 'step' => 0.125, 'default' => 1.0, 'unit' => 'rem'],
            ],
            'border' => [
                'width' => ['label' => __('Border width', 'elementor-extension-kit'), 'min' => 0, 'max' => 8, 'step' => 1, 'default' => 1, 'unit' => 'px'],
            ],
            'shadow' => [
                'level' => ['label' => __('Default elevation', 'elementor-extension-kit'), 'type' => 'select', 'default' => 'sm', 'options' => ['none' => __('None', 'elementor-extension-kit'), 'sm' => __('Soft', 'elementor-extension-kit'), 'md' => __('Medium', 'elementor-extension-kit'), 'lg' => __('Strong', 'elementor-extension-kit')]],
            ],
            'surface' => [
                'muted_strength' => ['label' => __('Muted surface strength', 'elementor-extension-kit'), 'min' => 0, 'max' => 16, 'step' => 1, 'default' => 4, 'unit' => '%'],
            ],
            'motion' => [
                'mode' => ['label' => __('Motion mode', 'elementor-extension-kit'), 'type' => 'select', 'default' => 'standard', 'options' => ['standard' => __('Standard', 'elementor-extension-kit'), 'subtle' => __('Subtle', 'elementor-extension-kit'), 'none' => __('None', 'elementor-extension-kit')]],
                'duration' => ['label' => __('Default duration', 'elementor-extension-kit'), 'min' => 0, 'max' => 1200, 'step' => 10, 'default' => 220, 'unit' => 'ms'],
                'easing' => ['label' => __('Default easing', 'elementor-extension-kit'), 'type' => 'select', 'default' => 'standard', 'options' => ['standard' => __('Standard', 'elementor-extension-kit'), 'emphasized' => __('Emphasized', 'elementor-extension-kit'), 'linear' => __('Linear', 'elementor-extension-kit')]],
            ],
        ];
    }
}
