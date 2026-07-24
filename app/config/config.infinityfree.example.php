<?php
/**
 * InfinityFree deployment override template.
 *
 * Copy this file to app/config/config.local.php on the hosting account,
 * then replace the placeholder values with the MySQL and SMTP details from
 * your InfinityFree control panel and email provider.
 */

define('DB_HOST', 'sqlXXX.infinityfree.com');
define('DB_PORT', '3306');
define('DB_USER', 'if0_XXXXXXXX');
define('DB_PASS', 'YOUR_INFINITYFREE_DATABASE_PASSWORD');
define('DB_NAME', 'if0_XXXXXXXX_pronetwork');

define('MAIL_BASE_URL', 'https://pronetwork.site.je');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'YOUR_GMAIL_APP_PASSWORD');
define('ADMIN_EMAIL', 'your-email@gmail.com');
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
