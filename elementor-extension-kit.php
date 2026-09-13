<?php
/**
 * Plugin Name: Elementor Extension Kit
 * Description: Reusable, modular Elementor extensions with self-contained element assets.
 * Version: 0.1.0
 * Requires PHP: 8.1
 * Requires at least: 6.5
 * Text Domain: elementor-extension-kit
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/src/Core/Autoloader.php';

\ElementorExtensionKit\Core\Autoloader::register();
\ElementorExtensionKit\Core\Plugin::boot(__FILE__);
