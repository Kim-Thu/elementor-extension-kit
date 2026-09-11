<?php

declare(strict_types=1);

namespace ElementorExtensionKit\Core;

final class Autoloader
{
    private const PREFIX = 'ElementorExtensionKit\\';

    public static function register(): void
    {
        spl_autoload_register(static function (string $class): void {
            if (! str_starts_with($class, self::PREFIX)) {
                return;
            }

            $relative = substr($class, strlen(self::PREFIX));

            if (
                $relative === ''
                || preg_match('/^(?:[A-Za-z_][A-Za-z0-9_]*\\\\)*[A-Za-z_][A-Za-z0-9_]*$/', $relative) !== 1
            ) {
                return;
            }

            $sourceRoot = realpath(dirname(__DIR__));

            if ($sourceRoot === false) {
                return;
            }

            $path = $sourceRoot . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
            $resolved = realpath($path);

            if (
                $resolved === false
                || ! is_file($resolved)
                || ! str_starts_with($resolved, $sourceRoot . DIRECTORY_SEPARATOR)
            ) {
                return;
            }

            require_once $resolved;
        });
    }
}
