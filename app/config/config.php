<?php
/**
 * Database Configuration and Constants - Dynamic / Git Version
 * This file is tracked in Git. Sensitive values (like passwords) are loaded
 * from 'config.local.php' locally or from Environment Variables in production.
 */

// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// Project Root
define('PROJECTROOT', dirname(APPROOT));
// Role Module Roots
define('ADMINROOT', PROJECTROOT . '/admin');
define('COMPANYROOT', PROJECTROOT . '/company');
define('USERROOT', PROJECTROOT . '/user');

// Site Name
define('SITENAME', 'ProNetwork');

// Dynamic URL Root detection (Works locally and on live hosting with or without public subfolder)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

if (strpos($requestUri, '/public/') !== false) {
    // If browser URL explicitly contains /public/
    $dir = '/public';
    if (strpos($scriptName, '/ProNetwork/') !== false) {
        $dir = '/ProNetwork/public';
    }
} elseif (strpos($scriptName, '/ProNetwork/') !== false) {
    // If local development under subfolder
    $dir = '/ProNetwork/public';
} else {
    // If running on root (like Railway production)
    $dir = '';
}

define('URLROOT', $protocol . '://' . $host . $dir);

// Load local overrides if they exist (ignored in git)
if (file_exists(APPROOT . '/config/config.local.php')) {
    require_once APPROOT . '/config/config.local.php';
}

// DB Params (Railway Environment Variables support, fallbacks to localhost or empty strings)
if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306');
}
if (!defined('DB_USER')) {
    define('DB_USER', $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'pronetwork');
}

// SMTP Config (Gmail SMTP, can be overridden by environment variables)
if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com');
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587);
}
if (!defined('SMTP_USER')) {
    define('SMTP_USER', $_ENV['SMTP_USER'] ?? getenv('SMTP_USER') ?? '');
}
if (!defined('SMTP_PASS')) {
    define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? getenv('SMTP_PASS') ?? '');
}
if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? getenv('ADMIN_EMAIL') ?? '');
}

// Outgoing Mail Customization
if (!defined('SMTP_FROM_EMAIL')) {
    define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?? 'noreply@pronetwork.com');
}
if (!defined('SMTP_FROM_NAME')) {
    define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME') ?? 'ProNetwork');
}