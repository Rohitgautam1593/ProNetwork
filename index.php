<?php
/**
 * Root index.php acting as a router fallback for servers without htaccess/mod_rewrite support (like Nixpacks/Nginx/Apache fallback).
 * This dynamically forwards requests to public/index.php securely without causing relative redirect loops.
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Get the base folder name (e.g. "/ProNetwork" or "")
$baseDir = dirname($scriptName);
$baseDir = str_replace('\\', '/', $baseDir);
if ($baseDir === '/' || $baseDir === '\\') {
    $baseDir = '';
} else {
    $baseDir = rtrim($baseDir, '/');
}

// Remove query string from REQUEST_URI to get the path
$path = parse_url($requestUri, PHP_URL_PATH);

// Strip the base directory if present
if ($baseDir !== '' && strpos($path, $baseDir) === 0) {
    $path = substr($path, strlen($baseDir));
}

// Strip the "/public" prefix if present
if (strpos($path, '/public') === 0) {
    $path = substr($path, 7); // Length of "/public"
}

// Strip leading and trailing slashes
$path = trim($path, '/');

// If the path is empty or refers to index.php, redirect to the public folder using an ABSOLUTE URL (prevents relative redirect loops)
if ($path === '' || $path === 'index.php') {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Add base directory back if present (for local environment compatibility)
    header('Location: ' . $protocol . '://' . $host . $baseDir . '/public/');
    exit;
}

// Set the 'url' parameter for the App router
$_GET['url'] = $path;

// Change working directory to public/ so relative paths inside public/index.php resolve correctly
chdir('public');
require_once 'index.php';
