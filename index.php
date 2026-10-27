<?php

/**
 * Amar Bangla Academy - Cross-Environment Entry Gateway
 * 
 * This file enables the project to run effortlessly on:
 * 1. Local PC (XAMPP / WAMP / Laragon) at http://localhost/Amar%20Bangla%20Academy/
 * 2. cPanel Shared Hosting (uploaded to public_html or subdirectory)
 * 3. Standard 'php artisan serve'
 * 
 * No server configuration or environment changes required!
 */

$publicPath = __DIR__ . '/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'
);

if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

require_once $publicPath . '/index.php';
