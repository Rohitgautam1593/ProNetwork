<?php
/**
 * Main application entry point
 */

// Start session
session_start();

// Redirect legacy /public/ URLs to clean root URLs in production to prevent asset and routing issues
$requestUriForRedirect = $_SERVER['REQUEST_URI'] ?? '';
$requestPathForRedirect = parse_url($requestUriForRedirect, PHP_URL_PATH) ?? '';
if (preg_match('#^/public(?:/|$)#', $requestPathForRedirect)) {
    $cleanPath = preg_replace('#^/public(?:/|$)#', '/', $requestPathForRedirect);
    $query = parse_url($requestUriForRedirect, PHP_URL_QUERY);
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    header('Location: ' . $protocol . '://' . $host . $cleanPath . ($query ? '?' . $query : ''), true, 301);
    exit;
}

// Require configuration
require_once '../app/config/config.php';

// Require Composer Autoloader
require_once '../vendor/autoload.php';

// Require core files
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';

// Load helpers
require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/MailHelper.php';
require_once '../app/helpers/image_helper.php';
require_once '../app/helpers/nav_helper.php';
require_once '../app/helpers/content_helper.php';
require_once '../app/helpers/CaptchaHelper.php';

// Fallback routing URL detection for Nginx servers without htaccess/mod_rewrite (e.g. Nixpacks/Railway)
if (!isset($_GET['url']) || $_GET['url'] === '') {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    
    $baseDir = dirname($scriptName);
    $baseDir = str_replace('\\', '/', $baseDir);
    if ($baseDir === '/' || $baseDir === '\\') {
        $baseDir = '';
    } else {
        $baseDir = rtrim($baseDir, '/');
    }
    
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    if ($baseDir !== '' && strpos($path, $baseDir) === 0) {
        $path = substr($path, strlen($baseDir));
    }
    
    if (strpos($path, '/public') === 0) {
        $path = substr($path, 7); // Length of "/public"
    }
    
    $path = trim($path, '/');
    
    if ($path !== 'index.php') {
        $_GET['url'] = $path;
    }
}

// Init Core App
$app = new App();
