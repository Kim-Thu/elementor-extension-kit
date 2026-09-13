<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Elementor;

final class ElementTemplateRegistry
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $templates = [];
        foreach (ElementManifestRepository::all() as $manifest) {
            $owner = is_string($manifest['id'] ?? null) ? $manifest['id'] : '';
            $manifestPath = is_string($manifest['_path'] ?? null) ? $manifest['_path'] : '';
            $definitions = is_array($manifest['templates'] ?? null) ? $manifest['templates'] : [];
            if ($owner === '' || $manifestPath === '') {
                continue;
            }

            $moduleRoot = realpath(dirname($manifestPath));
            if ($moduleRoot === false) {
                continue;
            }

            foreach ($definitions as $definition) {
                if (! is_array($definition)) {
                    continue;
                }
                $template = self::normalize($owner, $moduleRoot, $definition);
                if ($template === null || isset($templates[$owner][$template['id']])) {
                    continue;
                }
                $templates[$owner][$template['id']] = $template;
            }
        }

        return self::$cache = $templates;
    }

    public static function forElement(string $elementId): array
    {
        return self::all()[$elementId] ?? [];
    }

    public static function get(string $elementId, string $templateId): ?array
    {
        return self::forElement($elementId)[$templateId] ?? null;
    }

    private static function normalize(string $owner, string $moduleRoot, array $definition): ?array
    {
        $id = is_string($definition['id'] ?? null) ? trim($definition['id']) : '';
        $name = is_string($definition['name'] ?? null) ? trim($definition['name']) : '';
        $file = is_string($definition['file'] ?? null) ? trim(str_replace('\\', '/', $definition['file'])) : '';

        if (preg_match('/^[a-z][a-z0-9-]*$/', $id) !== 1 || $name === '' || $file === '') {
            return null;
        }
        if (str_starts_with($file, '/') || in_array('..', explode('/', $file), true)) {
            return null;
        }

        $resolved = realpath($moduleRoot . DIRECTORY_SEPARATOR . $file);
        if ($resolved === false || ! is_file($resolved) || ! str_starts_with($resolved, $moduleRoot . DIRECTORY_SEPARATOR)) {
            return null;
        }

        $modes = [];
        foreach ((array) ($definition['modes'] ?? []) as $mode) {
            $safe = sanitize_key((string) $mode);
            if ($safe !== '') {
                $modes[$safe] = $safe;
            }
        }

        return [
            'id' => $id,
            'name' => $name,
            'owner' => $owner,
            'file' => $file,
            'path' => $resolved,
            'modes' => array_values($modes),
        ];
    }
}
